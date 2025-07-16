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



    public function getAll($database_name){
        return $this->database->query('SELECT * FROM ?name', $database_name)->fetchAll();
    }


}
