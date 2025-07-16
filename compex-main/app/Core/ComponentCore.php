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
 

    // Files
    protected function createComponentFileTable(): \App\Components\FileTableControl {return new \App\Components\FileTableControl($this->model_core);}
    protected function createComponentFileItem(): \App\Components\FileItemControl {return new \App\Components\FileItemControl($this->model_core);}

    public function render()
    {

    }

    
}

