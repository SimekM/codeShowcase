<?php

declare(strict_types=1);

namespace App\UI;

use Nette;
use App;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;


use Nette\DI\Attributes\Inject;

abstract class _basePresenter extends Presenter
{

    #[Inject] public \App\Core\ModelCore $model_core;

    protected function createComponentCore(): App\Core\ComponentCoreControl
    {
        return new App\Core\ComponentCoreControl($this->model_core);
    }

   


    public function __construct() {}


    public function startup()
    {
        parent::startup();



        if (!$this->isAjax()) {
            $session = $this->getSession();

            if (!$session->isStarted()) {
                $session->start();
            }
        }

        $this->setMetadata();
        $this->template->addFilter('t', function ($key) {
            return $this->model_core->model_language->t($key, 'cs');
        });
    }





    private function setMetadata()
    {

        $this->template->meta_title = "Na koupališti sazovice";
        $this->template->meta_description = "Na koupališti sazovice";
        // $pageData = (object) null;

        // $whereKey = "presenter_key";
        // $whereValue = $this->getPresenter()->getName();
        // $databaseName = "presenter_meta_data";


        // $pageData->language = 'cs';
        // $pageData->whereKey = $whereKey;
        // $pageData->whereValue = $whereValue;
        // $pageData->databaseName = $databaseName;

        // $metaData = $this->model_core->getPageMetaData($pageData);

        // if (isset($metaData) && $metaData->title && $metaData->description) {
        //     $this->template->meta_title = $metaData->title;
        //     $this->template->meta_description = $metaData->description;

        // } else {

        // }

    }
}
