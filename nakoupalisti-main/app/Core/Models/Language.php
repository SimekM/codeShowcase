<?php

namespace App\Core\Models;

use Nette;


final class Language{
    

    public      $database;
    public      $context;
    public      $language_id = 'cs';
    public      $translate_values;

    public function __construct(Nette\Database\Explorer $database, Nette\Database\Context $context){
        $this->database = $database;
        $this->context = $context;
    }



    public function getPageMetaData($pageData) 
    { 
        $metadata = null;
        $paramsAll = [];

        $paramsAll['default']  = array ('meta_title_' . $pageData->language, 'meta_description_' . $pageData->language, $pageData->databaseName, $pageData->whereKey, $pageData->whereValue);

        $query = 'SELECT ?name AS title, ?name AS description FROM ?name WHERE ?name = ?';
        $metadata = $this->database->query($query, ...$paramsAll['default'])->fetch(); 
    
        return $metadata;
        
    }  


    public function getPresenterData($presenterName){
        $query = 'SELECT `name_cs` FROM presenter_meta_data WHERE presenter_key = ?';
        return $this->database->query($query, $presenterName)->fetchField(); 
    }






    public function getTranslatesAll($language_id)
    {   
        $query = "SELECT `id`, `" . $language_id . "` FROM `translates`";
        $translates = $this->database->query($query)->fetchPairs('id', $language_id);
        return $translates;
    }



    public function t($key, $language_id)
    {
        $this->language_id =  $language_id;

        if($this->translate_values == null ){

            $this->translate_values = (array)$this->getTranslatesAll($this->language_id);
        }
            
        if (isset($this->translate_values[$key])) {
            $value =  $this->translate_values[$key];
            return $value;
        }
       
        else{
            // $translate = $this->getValueByKey($key, $this->language_id);
            // if (!$translate){
            //     $source = "source/template";    
            //     $translate = $this->getValueByKey('translates', $key);
            // }    
            return null;

        }
    }  
    
    

   

    
    




  
}