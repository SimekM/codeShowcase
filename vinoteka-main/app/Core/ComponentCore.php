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



    protected function createComponentNotification(): \App\Components\NotificationSystemControl {return new \App\Components\NotificationSystemControl();}

    // UI
    protected function createComponentUiNavbar(): \App\Components\UiNavbarControl {return new \App\Components\UiNavbarControl($this->model_core);} 
    protected function createComponentUiFooter(): \App\Components\UiFooterControl {return new \App\Components\UiFooterControl();} 
    protected function createComponentUiEmail(): \App\Components\EmailControl {return new \App\Components\EmailControl();}
    protected function createComponentUiSInstagram(): \App\Components\UiSInstagramControl {return new \App\Components\UiSInstagramControl();}
    protected function createComponentUiSContact(): \App\Components\UiSContactControl {return new \App\Components\UiSContactControl();}
    protected function createComponentUiOrderSummary(): \App\Components\UiOrderSummaryControl {return new \App\Components\UiOrderSummaryControl();}
    protected function createComponentUiProdejna(): \App\Components\UiProdejnaControl {return new \App\Components\UiProdejnaControl();}

    // Products
    protected function createComponentProductTable(): \App\Components\ProductTableControl {return new \App\Components\ProductTableControl($this->model_core);} 

    protected function createComponentProductItem(): \App\Components\ProductItemControl {return new \App\Components\ProductItemControl($this->model_core);} 
    protected function createComponentCategoryItem(): \App\Components\CategoryItemControl {return new \App\Components\CategoryItemControl($this->model_core);}
    protected function createComponentProductCarousel(): \App\Components\ProductCarouselControl {return new \App\Components\ProductCarouselControl($this->model_core);}

    public function render()
    {

    }

    
}

