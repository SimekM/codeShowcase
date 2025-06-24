<?php
namespace App\Components;

use Nette;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpException;

class EmailControl extends Nette\Application\UI\Control
{



    public function __construct
            ( 
                
            )
        {

        }
        

    public function render(): void
    {
        $this->template->render(__DIR__ . '/form.latte');
    }





	public function onAjaxFormSuccess(Form $form): void
	{
		$this->redrawControl('flashes');

		if (!$this->presenter->isAjax()) {
			bdump('Non-AJAX request detected. The page has therefore been reloaded.', 'info');
			$this->redirect('this');
		}
	}



    protected function createComponentContactForm()
    {
        $form = new \Nette\Application\UI\Form();

        $form->addText('name', 'Jméno:')
            ->setRequired('Prosím, vyplňte jméno.')
            ->setAttribute('placeholder', 'Jméno:')        
            ->setAttribute('class', 'form-control');

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Prosím, vyplňte platný e-mail.')
            ->setAttribute('placeholder', 'E-mail:')        
            ->setAttribute('class', 'form-control');

        $form->addText('phone', 'Telefon:')
            ->setHtmlType('tel')
            ->setRequired('Prosím, vyplňte Telefon.')
            ->setAttribute('placeholder', 'Your Phone')
            ;
        $form->addTextArea('message', 'Zpráva:')
            ->setAttribute('placeholder', 'Zpráva')        
            ->setAttribute('class', 'form-control');


        $form->addReCaptcha(
            'captcha', // control name
            '', // label
            "Dokažte, že nejste robot." // error message
        );
    

        $form->addSubmit('send', 'Odeslat');

        $form->onSubmit[] = function () {
            
            $this->redrawControl('form-contact');
        };

        $form->onSuccess[] = [$this, 'sendEmail'];


        return $form;
    }

    public function sendEmail($form, $values) {

        $emailAccountInfo = (object)null;
        $emailAccountInfo->smtpServer   = 'smtp.seznam.cz';
        $emailAccountInfo->userName     = 'pekar@nodelogi.com';
        $emailAccountInfo->password     = 'f`WF+%e:;2Z,cKa]"C/=t<';
        $emailAccountInfo->smtpSecure   = 'ssl';

        $mailer = new Nette\Mail\SmtpMailer
        (
            host        : $emailAccountInfo->smtpServer,
            username    : $emailAccountInfo->userName,
            password    : $emailAccountInfo->password,
            encryption  : $emailAccountInfo->smtpSecure, 
        );

        $message = new Message();
        $message->setFrom($emailAccountInfo->userName)
                ->addTo('m.simek@nodelogi.com')
                ->setSubject('Nová zpráva z webu od ' .$values['name'])
                ->setBody('Jméno: '.$values['name'].'
                Email: '.$values['email'].'
                Zpráva: '.$values['message']);
            	
            	
        	try 
            	{ 
            	    $mailer->send($message); 
            	  	$myivResponse['error']	            = false;
            		$myivResponse['message']	        = "email_sent_success";
            	} 
        	catch (Nette\Mail\SmtpException $e) 
            	{   
            	    $myivResponse['error']	            = true;
            		$myivResponse['message']	        = "email_send_error";	
            		$myivResponse['mailer_exception']	= $e;
            		$myivResponse['mailer_exception_message'] = $e->getMessage();
            	}    
            
            bdump($myivResponse,"send email response");

            if ($myivResponse['error']){
                $this->template->flashMessage = 'Zpráva nebyla odeslána, prosím zkust to znovu';
        

            }else{
                $this->template->flashMessage = 'Zpráva úspěšně odeslána';
        

            }
            $this->redrawControl('form-contact');

		
        
    }



    public function handleSendForm(){
        // use sendEmail
        $data = (object)$_POST;
        if(isset($data->email) && isset($data->message) && isset($data->name) ){
            $this->sendEmail(null, [
                "name" => $data->name,
                "email" => $data->email,
                "message" => $data->message
            ]);
        };
    }

}
