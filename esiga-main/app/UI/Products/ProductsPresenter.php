<?php

declare(strict_types=1);

namespace App\UI\Products;

use Nette;
use App\UI\_basePresenter;
use Nette\DI\Attributes\Inject; 



final class ProductsPresenter extends _basePresenter
{
    #[Inject]     public \App\Core\ModelCore           $model_core;
    
    public function renderDefault($category, $page){



      
        if (!$this->isAjax()) {

        }

    }


    
   
 
    public function actionDetail($productCode, $category) : void 
    {
        $product = $this->model_core->model_shop->getProductBySlug($productCode);
        
        if ($product) {
            
            $categories = $this->model_core->model_shop->getCategoriesFromProductId($product->id);
            bdump($categories);
            if (isset($categories->subcategory->slug)){
                if ($categories->subcategory->slug === $category) {
                    $this->template->product = $product;
                    $this->template->categories = $categories;
                    $this->template->variations = $product->variations;
                } else {
                    throw new \Nette\Application\BadRequestException('Page not found', 404);
                }
            }else{
                if ($categories->category->slug === $category) {
                    $this->template->product = $product;
                    $this->template->categories = $categories;
                    $this->template->variations = $product->variations;
                    
                   
                } else {
                    throw new \Nette\Application\BadRequestException('Page not found', 404);
                }
            }
           
        } else {
            throw new \Nette\Application\BadRequestException('Page not found', 404);
        }
    }



    

}
