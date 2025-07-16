<?php

declare(strict_types=1);

namespace App\UI;

use Nette;
use App;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;


use Nette\DI\Attributes\Inject; 

abstract class _basePresenter extends Presenter
{

    #[Inject]     public \App\Core\ModelCore           $model_core;



    #[Persistent] public mixed                      $language_id = 'cs';
    #[Persistent] public mixed                      $languages = null; 
    #[Persistent] public mixed                      $translate_values = null; 
    
    public $myivSession = null;



    protected function createComponentCore(): App\Core\ComponentCoreControl {return new App\Core\ComponentCoreControl($this->model_core);} 

   
    

    
    
    public function __construct()
    {
        
    }

     
    public function startup()
    {     
        parent::startup(); 

        if (!$this->isAjax())
        {
            $baseComponentPath = realpath(__DIR__ . '/../Components/');
            $this->template->pathComponentBase = $baseComponentPath;

            $session = $this->getSession();

            if (!$session->isStarted()) {
                $session->start();
            }
            
        }

        $this->setupLanguage($this);


  
        $this->setMetadata();

    }
    

    public function setupLanguage($context): void
    {
        //$this->language_id = $this->getParameter('language_id');
        $context->template->language_id = $this->language_id;
        //if (isset($_SESSION['language_id']) ) { $this->language_id = $_SESSION['language_id']; } else { $_SESSION['language_id'] = $this->language_id; }
        if(!$this->translate_values) $this->setLanguage($this->language_id);
        $context->template->addFilter('t', function ($key)  { return $this->model_core->model_language->t($key, $this->language_id);   });
        $this->languages = array("cs");
        $context->template->translates = $this->translate_values;
    }

    public function setLanguage($language_id)
    {
        $_SESSION['language_id'] = $language_id;
        unset($this->translate_values);
        $this->model_core->model_language->setLanguage($language_id);  
        $this->translate_values = $this->model_core->model_language->getTranslatesAll($this->language_id);   
         
    }


    private function setMetadata(){


        $pageData = (object) null;
        
        $whereKey = "presenter_key";
        $whereValue = $this->getPresenter()->getName();
        $databaseName = "presenter_meta_data";

    
        $pageData->language =  $this->language_id;
        $pageData->whereKey = $whereKey;
        $pageData->whereValue = $whereValue;
        $pageData->databaseName = $databaseName;

        $metaData = $this->model_core->getPageMetaData($pageData);
        
        if (isset($metaData) && $metaData->title && $metaData->description) 
        {
            $this->template->meta_title = $metaData->title;
            $this->template->meta_description = $metaData->description;

        }else
        {
            $this->template->meta_title = "Nodelogi";
            $this->template->meta_description = "node";
      }

    }


   

   
    
    public function handleMyivcall()
    {  
        $request = (object)$_POST;
        bdump($request,"basePresenter_handleMyivcall POST:");
        $response           = (object)null;
        $response->error    = true;
        $response->message  = "nothing";
        if ($this->isAjax()) 
            {
                if(isset($request->action))
                    {
                        if( $request->action === 'ma_tady_zustat_v_presenteru') 
                        {

                        }
                        else 
                            { 
                                if(in_array($request->action,array("loadNewProducts")))
                                    { 
                                        $response = $this->getComponent('myiv')->getComponent('shopTable')->myivCall($request,$response); 

                                    }else if(in_array($request->agenda,array("cart")))
                                    {
                                        $response = $this->getComponent('myiv')->getComponent('shopDemand')->myivCall($request, $response);
                                    }
                            
                            }
                    }
                
                bdump($request,"------------- MYIVCALL REQUEST");
                bdump($response,"------------- MYIVCALL RESPONSE");
                $this->sendJson($response);

            }
        bdump($request,"------------- MYIVCALL REQUEST");
        bdump($response,"------------- MYIVCALL RESPONSE");

 
    }



}  