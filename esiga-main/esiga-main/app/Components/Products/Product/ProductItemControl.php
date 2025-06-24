<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class ProductItemControl extends Control
{
    public function __construct
    (
        private \App\Core\ModelCore          $model_core
    ) {
    }


    public function render($item): void
    {   
        $product_category = $this->model_core->model_shop->getCategoriesFromProductId($item->id);
        $cartId = $this->loadCartInfo();
       

        $this->template->cartId = $cartId;
        $this->template->render(__DIR__ . '/item.latte', ['product' => $item, 'categoryData'=> $product_category]);
        
    }   
       
    


        public function myivCall(object $request, object $response) : object
            {
                if ($request->action === "addNewCartItem")  
                {  
                    $response = $this->addItemToCart($request,$response);
                }
                else if($request->action === "RemoveFromCart")
                {
                    $this->removeFromCart($request,$response);
                }
                else if($request->action === "changeQuantity")
                {
                    $this->changeQuantity($request,$response);
                }        
                return $response;
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


    


}
