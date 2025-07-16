<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class FileTableControl extends Control
{
    public function __construct
    (               
         private  \App\Core\ModelCore $model_core
    ) {

    }


    public function render($presenterName): void
    {
        $table = ($presenterName === "Documents") ? "dokumenty" : "prispevkove_org";

        $filesArray = $this->model_core->model_query_database->getAll($table);
        $this->template->render(__DIR__ . '/table.latte', ['filesArray' => $filesArray, 'table' => $table]);

    }
}
