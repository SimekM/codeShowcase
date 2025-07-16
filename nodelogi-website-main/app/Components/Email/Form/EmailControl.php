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

    ) {

    }


    public function render(): void
    {
        $this->template->render(__DIR__ . '/form.latte');
    }




    public function sendEmail($form, $values)
    {

        $emailAccountInfo = (object) null;
        $emailAccountInfo->smtpServer = 'smtp.seznam.cz';
        $emailAccountInfo->userName = 'pekar@nodelogi.com';
        $emailAccountInfo->password = 'f`WF+%e:;2Z,cKa]"C/=t<';
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
            ->addTo('m.simek@nodelogi.com')
            ->setSubject('Nová zpráva z webu od ' . $values['name'])
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
        // Get the raw POST data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        /*
        $recaptchaToken = $data['recaptchaToken'];
        $secretKey = 'SECRET_KEY';

        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaToken}");
        $responseKeys = json_decode($response, true);

        if (intval($responseKeys["success"]) !== 1) {
            echo 'reCAPTCHA verification failed.';
            exit;
        }*/

        if (isset($data["email"]) && isset($data["message"]) && isset($data["name"])) {
            $myivResponse = $this->sendEmail(null, $data);
            bdump($myivResponse, "send email response");

            if ($myivResponse['error']) {
                /* $this->template->flashMessage = */
                echo 'Zpráva nebyla odeslána, prosím zkust to znovu';
                exit;
            } else {
                echo 'Zpráva úspěšně odeslána';
                exit;
            }
            ;

            /* $this->redrawControl('form-contact'); */
        } else {
            ;
            echo 'Zpráva nebyla odeslána, prosím zkust to znovu';
            exit;
        }
        ;
    }

}




// protected function createComponentContactForm()
// {
//     $form = new \Nette\Application\UI\Form();

//     $form->addText('name', 'Jméno:')
//         ->setRequired('Prosím, vyplňte jméno.')
//         ->setAttribute('placeholder', 'Jméno:')        
//         ->setAttribute('class', 'form-control');

//     $form->addEmail('email', 'E-mail:')
//         ->setRequired('Prosím, vyplňte platný e-mail.')
//         ->setAttribute('placeholder', 'E-mail:')        
//         ->setAttribute('class', 'form-control');

//     $form->addText('phone', 'Telefon:')
//         ->setHtmlType('tel')
//         ->setRequired('Prosím, vyplňte Telefon.')
//         ->setAttribute('placeholder', 'Your Phone')
//         ;
//     $form->addTextArea('message', 'Zpráva:')
//         ->setAttribute('placeholder', 'Zpráva')        
//         ->setAttribute('class', 'form-control');


//     $form->addReCaptcha(
//         'captcha', // control name
//         '', // label
//         "Dokažte, že nejste robot." // error message
//     );


//     $form->addSubmit('send', 'Odeslat');

//     $form->onSubmit[] = function () {

//         $this->redrawControl('form-contact');
//     };

//     $form->onSuccess[] = [$this, 'sendEmail'];


//     return $form;
// }