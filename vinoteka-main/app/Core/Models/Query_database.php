<?php

namespace App\Core\Models;

use Nette;
use Tracy\Debugger;
use Nette\Utils\FileSystem;

final class Query_database
{


    public function __construct
    (
          private Nette\Database\Explorer $database,
         private Nette\Database\Context $context
    ) {
   
    }



    public function getAll(string $database_name, ?int $limit){
        if ($limit) {
            return $this->database->query('SELECT * FROM ?name LIMIT ?', $database_name, $limit)->fetchAll();
        } else {
            return $this->database->query('SELECT * FROM ?name', $database_name)->fetchAll();
        }
    }


}
