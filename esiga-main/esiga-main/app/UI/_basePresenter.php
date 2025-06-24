<?php

declare(strict_types=1);

namespace App\UI;

use Nette;
use App;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;


use Nette\DI\Attributes\Inject;

abstract class _basePresenter extends Presenter
{

    #[Inject] public \App\Core\ModelCore $model_core;

    protected function createComponentCore(): App\Core\ComponentCoreControl
    {
        return new App\Core\ComponentCoreControl($this->model_core);
    }



    public function __construct()
    {

    }


    public function startup()
    {
        parent::startup();


        $this->template->categories = $this->model_core->model_shop->getCategories();

        if (!$this->isAjax()) {
            $session = $this->getSession();

            if (!$session->isStarted()) {
                $session->start();
            }

        }

        $this->setMetadata();

    }





    private function setMetadata()
    {


        $pageData = (object) null;

        $whereKey = "presenter_key";
        $whereValue = $this->getPresenter()->getName();
        $databaseName = "presenter_meta_data";


        $pageData->language = 'cs';
        $pageData->whereKey = $whereKey;
        $pageData->whereValue = $whereValue;
        $pageData->databaseName = $databaseName;

        $metaData = $this->model_core->getPageMetaData($pageData);

        if (isset($metaData) && $metaData->title && $metaData->description) {
            $this->template->meta_title = $metaData->title;
            $this->template->meta_description = $metaData->description;

        } else {
            $this->template->meta_title = "esiga";
            $this->template->meta_description = "node";
        }

    }


    public function handleAddToCart(){
        
        $cartId = $this->loadCartInfo();
        $data = (object) $_POST;

        $this->model_core->model_cart->addToCart($cartId, $data->productId, $data->quantity);
        $cartPrice = $this->model_core->model_cart->getCartTotal($cartId);

        $response =  [
            'error' => false,
            'message' => 'Produkt přidán do košíku',
            'cartTotal' => $cartPrice
        ];


       $this->sendJson($response);
    }


    public function loadCartInfo(): int
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

    public function getCategories()
    {
        $categories = $this->template->categories;
        return $categories;
    }




    public function getCategoryByUrl($slug)
    {
        $returnObject = (object) null;
        $categories = $this->template->categories; 
        
        foreach ($categories as $categoryData) {
            if ($categoryData->categories->slug == $slug) {
                $returnObject->categoryData = $categoryData->categories;
                $returnObject->type = 'categories';
                return $returnObject;
            }
            foreach ($categoryData->subcategories as $subCategory) {
                if ($subCategory->slug == $slug) {
                    $returnObject->categoryData = $subCategory;
                    $returnObject->type = 'subcategories';
    
                    return $returnObject;
                }
            }
        }
        return null;
    }
    
    public function handleMyivcall()
    {

        $this->flashMessage('This is a flash message!', 'success');

        if ($this->isAjax()){
            $this->redrawControl('flash');
        }
     
        $request = (object)$_POST;
        bdump($request,"basePresenter_handleMyivcall POST:");
        $response           = (object)null;
        $response->error    = true;
        $response->message  = "nothing";
        if ($this->isAjax()) 
            {
                if(isset($request->action))
                    {
                        if( $request->action === 'ma_tady_zustat_v_presenteru') 
                        {

                        }
                        else 
                            { 
                                if(in_array($request->action,array("loadNewProducts")))
                                    { 
                                        $response = $this->getComponent('core')->getComponent('shopTable')->myivCall($request,$response); 

                                    }else if(in_array($request->agenda,array("cart")))
                                    {
                                        $response = $this->getComponent('core')->getComponent('productCart')->myivCall($request, $response);
                                    }else if(in_array($request->agenda,array("product")))
                                    {
                                        $response = $this->getComponent('core')->getComponent('shopProduct')->myivCall($request, $response);
                                    }
                            
                            }
                    }
                
                bdump($request,"------------- MYIVCALL REQUEST");
                bdump($response,"------------- MYIVCALL RESPONSE");
                $this->sendJson($response);

            }
        bdump($request,"------------- MYIVCALL REQUEST");
        bdump($response,"------------- MYIVCALL RESPONSE");

 
    }

}