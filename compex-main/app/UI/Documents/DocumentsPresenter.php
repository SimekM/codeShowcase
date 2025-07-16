<?php

declare(strict_types=1);

namespace App\UI\Documents;

use Nette;
use App\UI\_basePresenter;



final class DocumentsPresenter extends _basePresenter
{
    public function renderDefault(){
        $links = $this->model_core->model_query_database->getAll("documents_urls");
        $this->template->links = $links;
    }

}
