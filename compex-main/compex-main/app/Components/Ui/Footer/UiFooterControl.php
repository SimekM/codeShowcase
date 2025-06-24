<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;



class UiFooterControl extends Control
{
    public function __construct
    (
    ) {

    }


    public function render(): void
    {
        $this->template->render(__DIR__ . '/footer.latte');
    }
}
