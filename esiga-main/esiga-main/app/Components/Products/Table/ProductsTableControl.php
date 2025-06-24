<?php
namespace App\Components;

use Nette;
use Tracy\Debugger;
use Nette\Application\UI\Control;
use Nette\Http\Url;

class ProductTableControl extends Control
{

    public $endOfList = false;
    
    public function __construct
            ( 

                private \App\Core\ModelCore          $model_core
            )
        {
        }
        

    public function render($repeatLoad = false): void
        {

            $data = (object)$_POST;
            $currentCategorySlug = null;

            $url = new Url($this->presenter->getHttpRequest()->getUrl()->getAbsoluteUrl());
            $cleanUrl = $url->getScheme() . '://' . $url->getAuthority() . $url->getPath();
            $this->template->cleanUrl = $cleanUrl;
            // Categories

            $this->template->categories = $this->presenter->getCategories();

            bdump($this->presenter->getCategories());
            if ($this->presenter->isAjax())
            {
                $parsedUrl = parse_url($data->url);
                $path = $parsedUrl['path'] ?? '';
                $pathParts = explode('/', trim($path, '/'));
                if (count($pathParts) >= 2) {
                    $currentCategorySlug = $pathParts[1]; // Get the category slug
                }

            }else
            {
            $currentCategorySlug = $this->presenter->getParameter('category');
            }

            $currentCategory = null;

            if ($currentCategorySlug === null) {
                $this->template->currentCategory = null;
            }else{
                $currentCategory = $this->presenter->getCategoryByUrl($currentCategorySlug);
                bdump($currentCategory);
                if ($currentCategory){
                    $this->template->currentCategory = $currentCategory->categoryData;
                }else{
                    //Category neexistuje
                    throw new BadRequestException('Page not found', 404);
                }

            }
        
            // Product loading

            $page = $this->presenter->getParameter('page') ?? ($data->parameters['page'] ?? 1);
            $searchText = $this->presenter->getParameter('searchText') ?? ($data->parameters['searchText'] ?? '');

            
            $productsArray = $this->getProducts($currentCategory, $page, $searchText);


            if ($page < 1 || ($productsArray->pages > 0 && $page > $productsArray->pages)) {
                throw new BadRequestException('Page not found', 404);
            }

            $this->template->repeatLoad = $repeatLoad;
            $this->template->products = $productsArray;
            $this->template->endOfList = $productsArray->endOfList;
            $this->endOfList = $this->template->endOfList;
            $this->template->render(__DIR__ . '/table.latte');
               
        }

    protected function loadCartInfo(): int
        {
            $cartSession = $this->presenter->getSession()->getSection('cart');
            $cartId = null;

            if (isset($cartSession->cartId)) {
                $cartId = $cartSession->cartId;
            } else {
                $sessionId = $this->presenter->getSession()->getId();
                $cartInfo = $this->model_core->model_cart->getOrCreateCartBySessionId($sessionId);

                $cartSession->cartId = $cartInfo['id'];
                $cartId = $cartInfo['id'];
            }

            return $cartId;

        }    

 
    
    public function getProducts($category, int $page, ?string $searchText):object
        {    
            $cartId=$this->loadCartInfo();
            return $this->model_core->model_shop->getProducts($category, $page, $cartId, $searchText); 
        }
        
    public function myivCall(object $request,object $response):object
        {
            if ($request->action === "loadNewProducts")  {  $response = $this->loadNewProducts($request,$response);              }      
            return $response;
         } 

    public function loadNewProducts($request,$response)
        {
            
            $targetElementId = $request->targetElementId;
            ob_start();
            $this->render(true);
            $html = ob_get_clean(); 

            
            $response               = (object)null;
            $response->agenda       = "shop";
            $response->agenda_id    = null;
            $response->htmlString   = $html;
            $response->modelMessage = "myivCall_getEdit done";
            $endOfList =  $this->endOfList ? 'true' : 'false';

            $equalsSing = "+=";

            if ($request->parameters["page"] === '1' && !empty($request->parameters["searchText"])){
                $equalsSing = "=";
            }

            $getParamters = (object) null;
            $getParamters->page = $request->parameters["page"];

            if (!empty($request->parameters["searchText"])){
                $getParamters->searchText = $request->parameters["searchText"];
            }


            $response->callBack     = 
                '
                    targetElementId = "'.$targetElementId.'"
                    var element = document.getElementById(targetElementId);
                    element.innerHTML'.$equalsSing.'response.htmlString;  
                    setupObserver();
                    observeAllPages(); 
                    loading = false;
                    endOfList = ' .$endOfList. ';';
                    

           

            return($response);

        }



    public function getTableSelection():object
        {    
            return $this->model_core->model_shop->getTableSelection("products"); 
        }            


      
}    