<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class CategoryItemControl extends Control
{
    public function __construct
    (
    ) {

    }


    public function render(): void {

        $currentCategory = $this->presenter->getCategoryByUrl($this->presenter->getParameter('category'));
        $categories = $this->presenter->getCategories();
        $this->template->render(__DIR__ . '/item.latte', ['categories' => $categories, 'currentCategory'=> $currentCategory]);
        // $this->template->render(__DIR__ . '/item.latte', []);
    }
}
