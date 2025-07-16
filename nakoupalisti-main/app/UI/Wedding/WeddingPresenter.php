<?php

declare(strict_types=1);

namespace App\UI\Wedding;

use Nette;
use App\UI\_basePresenter;
use Nette\Mail\Message;
use Nette\DI\Attributes\Inject; 


final class WeddingPresenter extends _basePresenter
{

    #[Inject]     public \App\Core\ModelCore           $model_core;




    public function renderDefault()
    {

    }

   
}
