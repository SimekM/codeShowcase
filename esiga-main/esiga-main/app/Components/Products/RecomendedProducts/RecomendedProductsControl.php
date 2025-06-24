<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class RecomendedProductsControl extends Control
{
    public function __construct
    (
        private \App\Core\ModelCore          $model_core

    ) {

    }


    public function render($presenter = "home"): void {
        if($presenter == "home"){
            $this->template->products = $this->model_core->model_shop->getRecommendedProducts();
        }
        $this->template->render(__DIR__ . '/table.latte', []);
    }
}
