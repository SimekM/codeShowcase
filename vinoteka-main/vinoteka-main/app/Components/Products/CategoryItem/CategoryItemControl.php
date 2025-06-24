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


    public function render($category): void {

      $this->template->render(__DIR__ . '/item.latte', ['category' => $category]);
    }
}
