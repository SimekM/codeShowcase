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

        $this->template->meta_title = "Vinotéka - králova vila";
        $this->template->meta_description = "Účast na Vašich pořádajících prezentacích Účast na Vašich pořádajících prezentacích  Účast na Vašich pořádajících prezentacích  ";

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
    
    public function getCategories()
    {
        $categories = $this->template->categories;
        return $categories;
    }



    public function getCategoryByUrl($slug)
    {
        $returnObject = (object) null;
    
        // This is the array of categories objects
        $categories = $this->template->categories;
    
        foreach ($categories as $categoryData) {
    
            // Check if the main category's slug matches
            if (isset($categoryData->categories) && $categoryData->categories->slug === $slug) {
                $returnObject->categoryData = $categoryData->categories;
                $returnObject->type = 'categories';
                return $returnObject;
            }
    
            // Check subcategories if they exist
            if (!empty($categoryData->subcategories)) {
                foreach ($categoryData->subcategories as $subCategory) {
                    if ($subCategory->slug === $slug) {
                        $returnObject->categoryData = $subCategory;
                        $returnObject->type = 'subcategories';
                        return $returnObject;
                    }
                }
            }
        }
    
        // Return null if nothing matched
        return null;
    }
    

}