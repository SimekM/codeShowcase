<?php
namespace App\Core;

use Nette;
use Nette\Application\UI\Control;
use Nette\Utils\Html;

class ComponentCoreControl extends Control
{
   

    public function __construct
    ( 
         private \App\Core\ModelCore $model_core

    )
    {
    }

    // UI
    protected function createComponentUiNavbar(): \App\Components\UiNavbarControl {return new \App\Components\UiNavbarControl($this->model_core);} 
    protected function createComponentUiFooter(): \App\Components\UiFooterControl {return new \App\Components\UiFooterControl();} 
    protected function createComponentBreadcrumb(): \App\Components\UiBreadCrumbsControl {return new \App\Components\UiBreadCrumbsControl($this->model_core);} 

    /* Product */
    protected function createComponentNotification(): \App\Components\NotificationSystemControl {return new \App\Components\NotificationSystemControl($this->model_core);} 
    protected function createComponentPopupModal(): \App\Components\PopupSystem {return new \App\Components\PopupSystem($this->model_core);} 



    public function render()
    {

    }

    
}

