<?php

declare(strict_types=1);

namespace App\UI\Support;

use Nette;
use App\UI\_basePresenter;



final class SupportPresenter extends _basePresenter
{
    public function renderDefault(){
        $textArray = $this->model_core->model_query_database->getAll("support_texts");
        $this->template->textArray = $textArray;
    }

}
