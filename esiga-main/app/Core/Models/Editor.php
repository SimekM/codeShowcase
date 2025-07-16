<?php

namespace App\Core\Models;

use Nette;

use Nette\Database\Context;

final class Editor 
{

    public      $database;

    public function __construct(Nette\Database\Explorer $database){
        $this->database = $database;
    }


   

  
}