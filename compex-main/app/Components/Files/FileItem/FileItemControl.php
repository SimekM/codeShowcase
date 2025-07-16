<?php
namespace App\Components;

use Nette;
use Nette\Application\UI\Control;
use Nette\DI\Attributes\Inject;
use App\UI\_basePresenter;


class FileItemControl extends Control
{
    public function __construct
    (
    ) {

    }


    public function render($table, $file): void
    {
        $filePath = __DIR__ . '/../../../../www/'. $table .'/' . $file->url;
 
        if (file_exists($filePath)) 
        {
            $lastModified = filemtime($filePath);
            $lastModifiedFormatted = date("j. n. Y", $lastModified);
            $fileSize = filesize($filePath);
            $fileSizeKB = round($fileSize / 1024, 2);

            $file->fileSize = $fileSizeKB;
            $file->lastModified = $lastModifiedFormatted;

        }

        $this->template->render(__DIR__ . '/item.latte', ['table' => $table, 'file' => $file]);
    }
}
