<?php

declare(strict_types=1);

namespace App\UI\Menu;

use Nette;
use App\UI\_basePresenter;
use Nette\Mail\Message;
use Nette\DI\Attributes\Inject;


final class MenuPresenter extends _basePresenter
{

    #[Inject]     public \App\Core\ModelCore           $model_core;




    /**
     * Menu page action
     * @return void
     */
    public function renderDefault(): void
    {

      
    }
}
