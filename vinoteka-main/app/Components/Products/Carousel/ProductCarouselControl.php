<?php
namespace App\Components;

use Nette;
use Tracy\Debugger;
use Nette\Application\UI\Control;
use Nette\Http\Url;

class ProductCarouselControl extends Control
{

    public $endOfList = false;
    
    public function __construct
            ( 

                private \App\Core\ModelCore          $model_core
            )
        {
        }
        
    

    public function render($repeatLoad = false): void
        {
            // Product loading
            $productsArray = $this->model_core->model_query_database->getAll("products", 10);

            $this->template->repeatLoad = $repeatLoad;
            $this->template->products = $productsArray;
            $this->template->render(__DIR__ . '/carousel.latte');
               
        }

}