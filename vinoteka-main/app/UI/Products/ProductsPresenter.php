<?php

declare(strict_types=1);

namespace App\UI\Products;

use Nette;
use App\UI\_basePresenter;
use Nette\DI\Attributes\Inject; 



final class ProductsPresenter extends _basePresenter
{
    #[Inject]     public \App\Core\ModelCore           $model_core;

    public function renderDefault(){


        $this->template->currentCategory = $this->presenter->getParameter('category');
        $this->template->categories = $this->presenter->getCategories();

    }


    public function actionDetail($productCode, $category) : void 
    {
        $product = $this->model_core->model_shop->getProductBySlug($productCode);

        if ($product) {
            $categories = $this->model_core->model_shop->getProductCategoriesHierarchy($product->product->id);
            bdump($categories);
            if ($categories->wine_type->slug === $category) {
                $this->template->product = $product->product;
                $this->template->categories = $categories;
                $this->template->attributes = $product->attributes;
              
               
            } else {
                throw new \Nette\Application\BadRequestException('Page not found', 404);
            }
        } else {
            throw new \Nette\Application\BadRequestException('Page not found', 404);
        }
    }

}
