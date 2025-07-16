<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class UiNavbarControl extends Control
{






    public function __construct
    (
        private \App\Core\ModelCore $model_core,
    ) {
        
    }


    public function render(): void
    {
        $this->presenter->setupLanguage($this);
        
        $this->template->render(__DIR__ . '/navbar.latte');
    }

}
