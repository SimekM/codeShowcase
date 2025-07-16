<?php

declare(strict_types=1);

namespace App\UI\Login;

use Nette;
use Tracy;
use Tracy\Debugger;
use Nette\Application\UI\Form;


use App\Core\Models\Admin;
use Nette\DI\Attributes\Inject; 



final class LoginPresenter extends Nette\Application\UI\Presenter
{
    #[Inject]     public Admin      $model_admin;


    public function startup()
    {
        parent::startup();

       $baseAssetsPath = realpath(__DIR__ . '/../_assets');
       $this->template->baseAssetsPath = $baseAssetsPath;
        
      
       
    }

    public function renderDefault(){
       
    }



    public function handleLogIn(): void
    {
        $data = (object)$_POST;
     
        try {
            $user = $this->model_admin->authenticate($data->email, $data->password);
            if (!$user['message']['error']){
            $this->getUser()->login($user['identity']);
            }
           	$response = $user['message'];
        } catch (AuthenticationException $e) {
            $response =  [
                'error' => true,
                'message' => 'Přihlášení se nezdařilo'
            ];
        }

        if ($this->isAjax()) {
            $this->sendJson($response);
        } else {
            $this->redirect('this');
        }
    }


}