<?php

declare(strict_types=1);

namespace App\Core;

use Nette;



class ModelCore
{
  
      public function __construct
        (
          private Nette\Database\Explorer       $database,
          private Nette\Database\Context        $context,
          public \App\Core\Models\Language      $model_language,
          public \App\Core\Models\Admin      $model_admin,


        )
        {
            
        }

        public function getPageMetaData($pageData) {return $this->model_language->getPageMetaData($pageData);}


}



