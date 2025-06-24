<?php

declare(strict_types=1);

namespace App\UI\Profile;

use Nette;
use App\UI\_basePresenter;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpException;


final class ProfilePresenter extends _basePresenter
{
    public function renderDefault(){
    }

}
