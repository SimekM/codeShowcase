<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\Utils\Html;


class UiKeyBottomControl extends \Nette\Application\UI\Control
{

    public function __construct
    (
    ) {

    }


    public function render()
    {
        $this->template->render(__DIR__ . '/default.latte', []);
    }

}
