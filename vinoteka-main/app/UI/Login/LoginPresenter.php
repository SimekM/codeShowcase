<?php

declare(strict_types=1);

namespace App\UI\Login;

use Nette;
use App\UI\_basePresenter;
use Nette\DI\Attributes\Inject; 
use App\Core\Models\Admin;


final class LoginPresenter extends _basePresenter
{
  
    #[Inject]     public Admin      $model_admin;


    public function startup()
    {
        parent::startup();

        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Admin:default');
        }
       
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
