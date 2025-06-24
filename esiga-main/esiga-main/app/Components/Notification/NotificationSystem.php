<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class NotificationSystemControl extends Control
{
    public function __construct
    (
    ) {

    }


    public function render(): void
    {
           $this->template->render(__DIR__ . '/layout.latte', []);
    }
}
