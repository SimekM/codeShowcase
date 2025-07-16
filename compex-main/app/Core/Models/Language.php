<?php

namespace App\Core\Models;

use Nette;


final class Language{
    
    public      $language_id = "cs";   
    public      $translate_values; 

    public      $product_spec_translate_values; 

    public      $database;
    public      $context;

    public function __construct(Nette\Database\Explorer $database, Nette\Database\Context $context){
        $this->database = $database;
        $this->context = $context;
    }


    
    public function setLanguage($language_id) { $this->language_id = $language_id; }  
    
    
    public function getPageMetaData($pageData) 
    { 
        $metadata = null;
        $paramsAll = [];

        $paramsAll['default']  = array ('meta_title_' . $pageData->language, 'meta_description_' . $pageData->language, $pageData->databaseName, $pageData->whereKey, $pageData->whereValue);

        $query = 'SELECT ?name AS title, ?name AS description FROM ?name WHERE ?name = ?';
        $metadata = $this->database->query($query, ...$paramsAll['default'])->fetch(); 
    
        return $metadata;
        
    }  



    public function getTranslatesAll($language_id)
    {   
        $query = "SELECT `text_key`, ?name FROM `translates`";
        $translates = $this->database->query($query, $language_id)->fetchPairs('text_key', $language_id);;
        return $translates;
    }



    public function t($key,$language_id)
    {
        $this->language_id =  $language_id;

        if( $this->translate_values == null ){

            $this->translate_values = (array)$this->getTranslatesAll($this->language_id);
        }
            
        if (isset($this->translate_values[$key])) {
            $value =  $this->translate_values[$key];
            return $value;
        }
       
        else{
            $translate = $this->getValueByKey($this->language_id, $key);
            return $translate;
        }
    }  
    
    

   

    public function getValueByKey($language_key, $text_key)
    {
        $query = 'SELECT ?name FROM translates WHERE `text_key` = ?';
    
        return $this->database->query($query, $language_key, $text_key)->fetchField(); 
    }
    




  
}