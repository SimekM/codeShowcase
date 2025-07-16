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
        private \App\Core\ModelCore          $model_core,
    ) {

    }


    public function render(): void
    {
        $presenterName = $this->presenter->getName();
        $presenterAction = $this->presenter->getAction();
        $this->template->categories = $this->model_core->model_shop->getCategories();
        bdump($this->template->categories);
        $this->template->render(__DIR__ . '/navbar.latte', ['presenterName' => $presenterName, 'presenterAction' => $presenterAction]);
    }
}
