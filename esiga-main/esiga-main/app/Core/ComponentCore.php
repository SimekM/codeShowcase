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
    protected function createComponentUiKeyBottom(): \App\Components\UiKeyBottomControl {return new \App\Components\UiKeyBottomControl();} 
    protected function createComponentUiOrderSummary(): \App\Components\UiOrderSummaryControl {return new \App\Components\UiOrderSummaryControl();}
    protected function createComponentNotification(): \App\Components\NotificationSystemControl {return new \App\Components\NotificationSystemControl();} 

    /* Product */
    protected function createComponentProductTable(): \App\Components\ProductTableControl {return new \App\Components\ProductTableControl($this->model_core);} 
    protected function createComponentProductItem(): \App\Components\ProductItemControl {return new \App\Components\ProductItemControl($this->model_core);} 

    protected function createComponentCategoryItem(): \App\Components\CategoryItemControl {return new \App\Components\CategoryItemControl($this->model_core);} 
    
    protected function createComponentRecomendedProducts(): \App\Components\RecomendedProductsControl {return new \App\Components\RecomendedProductsControl($this->model_core);} 

    protected function createComponentRecomItem(): \App\Components\RecomItemControl {return new \App\Components\RecomItemControl($this->model_core);} 


    // Files
    protected function createComponentFileTable(): \App\Components\FileTableControl {return new \App\Components\FileTableControl($this->model_core);}
    protected function createComponentFileItem(): \App\Components\FileItemControl {return new \App\Components\FileItemControl($this->model_core);}

    public function render()
    {

    }

    
}

