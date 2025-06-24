<?php

declare(strict_types=1);

namespace App\UI\Contact;

use Nette;
use App\UI\_basePresenter;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpException;


final class ContactPresenter extends _basePresenter
{
    public function renderDefault(){
    }

    public function handleSendForm() {

        $values = (array) $_POST;
        $response = [
            'error' => false,
            'message' => 'Zprávu se nepodařilo odeslat zkuste to prosím později.'
        ];

        $emailAccountInfo = (object)null;
        $emailAccountInfo->smtpServer   = 'smtp.seznam.cz';
        $emailAccountInfo->userName     = 'noreply@nodelogi.com';
        //$emailAccountInfo->password     = 'Yv_W6}EzZG!Q`rSFf4%@';
        $emailAccountInfo->password = '';
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
                ->setSubject('Zpráva z webu: ' .$values['subject'])
                ->setBody('Jméno: '.$values['name'].'
                Email: '.$values['email'].'
                Telofon: '.$values['phone'].'
                Firma: '.$values['company'].'
                Zpráva: '.$values['message']);
            	
            	
        	try 
            	{ 
            	    $mailer->send($message); 
                    $response = [
                        'error' => false,
                        'message' => 'Úspěšně odesláno.'
                    ];
            	} 
        	catch (Nette\Mail\SmtpException $e) 
            	{   
                    $response = [
                        'error' => false,
                        'message' => 'Zprávu se nepodařilo odeslat zkuste to prosím později.'
                    ];
                    bdump($e->getMessage());
            		
            	}        

            $this->sendJson($response);
        
        }

}
