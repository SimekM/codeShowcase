<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class UiNavbarControl extends Control
{
    public function __construct
    (
        private \App\Core\ModelCore          $model_core
    ) {

    }


    public function render(): void
    {
        $presenterName = $this->presenter->getName();
        $presenterAction = $this->presenter->getAction();

        $cartId = $this->presenter->loadCartInfo();
        $cartPrice = $this->model_core->model_cart->getCartTotal($cartId);

        $this->template->render(__DIR__ . '/navbar.latte', ['presenterName' => $presenterName, 'presenterAction' => $presenterAction, 'cartTotal' => $cartPrice]);
    }

    
}
