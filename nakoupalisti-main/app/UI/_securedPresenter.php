<?php

declare(strict_types=1);

namespace App\UI;

use Nette;
use Nette\Application\UI\Presenter;
use App;
use Tracy\Debugger;
use Nette\DI\Attributes\Inject;

abstract class _securedPresenter extends Presenter
{

    public function __construct()    
        {

        }
     
    public function startup()
        {     
            parent::startup(); 

            if (!$this->getUser()->isLoggedIn()) 
		    {
			    $this->redirect('Login:default'); 
			}
            
        }



    }       