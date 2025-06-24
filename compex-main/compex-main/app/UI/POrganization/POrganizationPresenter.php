<?php

declare(strict_types=1);

namespace App\UI\POrganization;

use Nette;
use App\UI\_basePresenter;



final class POrganizationPresenter extends _basePresenter
{
    public function renderDefault()
    {
        $links = $this->model_core->model_query_database->getAll("prispevkove_org_urls");
        $this->template->links = $links;
    }

    public function handleUploadFile(): void
    {
        $file = $this->getHttpRequest()->getFile('file');
        $response = null;
        if ($file && $file->isOk()) {
            $timestamp = time(); 
            $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
            $destination = __DIR__ . '/../../../www/userUploads/' . $timestamp . ".txt";
            $file->move($destination);
            $response = [
                'error' => false,
                'message' => 'Úspěšně odesláno.'
            ];
        } else {
            // Respond with an error if the file is not valid
            $response = [
                'error' => true,
                'message' => 'Nepodařilo se soubor odeslat zkuste to prosím znovu.'
            ];
        }

        

        $this->sendJson($response);
    }
    
}
