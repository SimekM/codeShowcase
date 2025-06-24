<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\Utils\Html;


class UiBreadCrumbsControl extends \Nette\Application\UI\Control
{
    private $items = [];
    private $language = 'cs';
    


    public function __construct
    ( 
        private \App\Core\ModelCore          $model_core
    )
    {
    }


    public function render()
    {
    
        $presenterName   = $this->presenter->getPresenter()->name;
        //$presenterAction   = $this->presenter->getPresenter()->getAction();
        //$presenterTitle = $this->model_core->model_language->getPresenterData($presenterName);
        $this->items = "";
        $this->template->render(__DIR__ . '/breadCrumbs.latte', ['items' => $this->items]);
    }

}
