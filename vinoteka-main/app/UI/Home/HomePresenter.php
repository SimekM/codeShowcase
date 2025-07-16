<?php

declare(strict_types=1);

namespace App\UI\Home;

use Nette;
use App\UI\_basePresenter;
use Nette\Mail\Message;


final class HomePresenter extends _basePresenter
{



    public function renderDefault()
    {

    
    }

    public function sendEmail($values)
    {

        $emailAccountInfo = (object) null;
        $emailAccountInfo->smtpServer = 'smtp.seznam.cz';
        $emailAccountInfo->userName = '';
        $emailAccountInfo->password = '';
        $emailAccountInfo->smtpSecure = 'ssl';

        $mailer = new Nette\Mail\SmtpMailer
        (
            host: $emailAccountInfo->smtpServer,
            username: $emailAccountInfo->userName,
            password: $emailAccountInfo->password,
            encryption: $emailAccountInfo->smtpSecure,
        );

        $message = new Message();
        $message->setFrom($emailAccountInfo->userName)
            ->setSubject('Nová zpráva z webu: ' . $values["subject"])
            ->setBody('Jméno: ' . $values['name'] . '
                Email: ' . $values['email'] . '
                Zpráva: ' . $values['message']);


        try {
            $mailer->send($message);
            $myivResponse['error'] = false;
            $myivResponse['message'] = "email_sent_success";
        } catch (Nette\Mail\SmtpException $e) {
            $myivResponse['error'] = true;
            $myivResponse['message'] = "email_send_error";
            $myivResponse['mailer_exception'] = $e;
            $myivResponse['mailer_exception_message'] = $e->getMessage();
        }
        ;

        return $myivResponse;
    }


    public function handleSendForm()
    {
        echo "Emailový účet není nastavený...";
        exit;

        // Get the raw POST data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data["email"]) && isset($data["subject"]) && isset($data["message"]) && isset($data["name"])) {
            $myivResponse = $this->sendEmail($data);
            bdump($myivResponse, "send email response");

            if ($myivResponse['error']) {
                /* $this->template->flashMessage = */
                echo 'Zpráva nebyla odeslána, prosím zkuste to znovu';
                exit;
            } else {
                echo 'Zpráva úspěšně odeslána';
                exit;
            }
            ;

        } else {
            echo 'Zpráva nebyla odeslána, prosím zkuste to znovu';
            exit;
        }
        ;
    }
}
