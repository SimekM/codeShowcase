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

    protected function createComponentUiNavbar(): \App\Components\UiNavbarControl {return new \App\Components\UiNavbarControl($this->model_core);} 
    protected function createComponentUiFooter(): \App\Components\UiFooterControl {return new \App\Components\UiFooterControl();} 
    protected function createComponentEmailForm(): \App\Components\EmailControl {return new \App\Components\EmailControl();} 


    public function render()
    {

    }

    
}

