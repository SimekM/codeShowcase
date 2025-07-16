<?php

declare(strict_types=1);

namespace App\UI\Admin;

use Nette;
use App\UI\_basePresenter;
use App\UI\_securedPresenter;
use Nette\DI\Attributes\Inject; 
use App\Core\Models\Admin;

final class AdminPresenter extends _securedPresenter
{

    #[Inject]     public Admin      $model_admin;


    private $emptyConfigArray = [

        'config' => [
            'query_tables' => [
            ]
        ],

        'main_tables' => [  

            'name' => [
                'properties' => [
                    'type' => 'main_table',
                    'visibility' => 'visible',
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    
                ]
            ],


            'relationships_table' => [
                'properties' => [
                    'title' => 'Kategorie produktu',
                    'type' => 'relationships_table',
                    'visibility' => 'hidden',
                    'reference_table' => 'products',
                    'where_clause' => 'product_id',
                    'data_fill_table' => 'tags',
                    'data_fill_table_reference_value' => 'tag_id',

                    'selection' => 'tag_id'
                ],
                'variables' => [
                    'product_id' => ['type' => 'id', 'visibility' => 'hidden'],
                    'tag_id' => ['type' => 'foreinKey', 'foreinKeyTable' => 'tags', 'title' => 'Kategorie produktu', 'visibility' => 'hidden'],
                ]
            ],


            'child_table' => [
                'properties' => [
                    'title' => 'Popisky produktu',
                    'type' => 'child_table',
                    'visibility' => 'hidden',
                    'where_clause' => 'product_id',
                    'reference_table' => 'products',
                    'selection' => 'id, description, description_order',

                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'product_id' => ['type' => 'id', 'visibility' => 'hidden', 'selection' => false],
                ]
            ],
        ]

    ];











    private $productsConfigArray = [

        'config' => [
            'query_tables' => [
                'tags' => ['selection' => 'id, title_cs']
            ]
        ],

        'main_tables' => [  

            'products' => [
                'properties' => [
                    'type' => 'main_table',
                    'visibility' => 'visible',
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'slug' => ['type' => 'text', 'title' => 'Url'],
                    'img_src' => ['type' => 'image', 'title' => 'Obrázek', 'visibility' => 'hidden'],
                    'stockCount' => ['type' => 'number', 'title' => 'Skladem'],
                    'price' => ['type' => 'number', 'title' => 'Cena'],
                    'title_cs' => ['type' => 'text', 'title' => 'Název produktu'],
                    'subtitle' => ['type' => 'text', 'title' => 'Podnadpis produktu'],
                    'meta_title_cs' => ['type' => 'text', 'title' => 'Meta title stránky', 'visibility' => 'hidden'],
                    'meta_description_cs' => ['type' => 'text', 'title' => 'Meta popisek stránky', 'visibility' => 'hidden'],
                ]
            ],


            'tags_relationships' => [
                'properties' => [
                    'title' => 'Kategorie produktu',
                    'type' => 'relationships_table',
                    'visibility' => 'hidden',
                    'reference_table' => 'products',
                    'where_clause' => 'product_id',
                    'data_fill_table' => 'tags',
                    'data_fill_table_reference_value' => 'tag_id',

                    'selection' => 'tag_id'
                ],
                'variables' => [
                    'product_id' => ['type' => 'id', 'visibility' => 'hidden'],
                    'tag_id' => ['type' => 'foreinKey', 'foreinKeyTable' => 'tags', 'title' => 'Kategorie produktu', 'visibility' => 'hidden'],
                ]
            ],


            'product_descriptions' => [
                'properties' => [
                    'title' => 'Popisky produktu',
                    'type' => 'child_table',
                    'visibility' => 'hidden',
                    'where_clause' => 'product_id',
                    'reference_table' => 'products',
                    'selection' => 'id, description, description_order',

                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'product_id' => ['type' => 'id', 'visibility' => 'hidden', 'selection' => false],
                    'description' => ['type' => 'text', 'title' => 'Popis'],
                    'description_order' => ['type' => 'position', 'title' => 'Pořadí']
                ]
                ],



            'product_variations' => [
                'properties' => [
                    'title' => 'Variace produktu',
                    'type' => 'child_table',
                    'visibility' => 'hidden',
                    'reference_table' => 'products',
                    'where_clause' => 'product_id',
                    'selection' => 'id, attribute_name, attribute_value, attribute_detail, price_adjustment'
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'product_id' => ['type' => 'id', 'visibility' => 'hidden', 'selection' => false],
                    'attribute_name' => ['type' => 'text', 'title' => 'Název atributu'],
                    'attribute_value' => ['type' => 'text', 'title' => 'Hodnota atributu'],
                    'attribute_detail' => ['type' => 'text', 'title' => 'Detail atributu'],
                    'price_adjustment' => ['type' => 'number', 'title' => 'Přičtení k ceně']
                ]
            ]          
        ]


    ];



















    private $productCategoriesConfigArray = [

        'config' => [
            'query_tables' => [
            ]
        ],

        'main_tables' => [  

            'tags' => [
                'properties' => [
                    'type' => 'main_table',
                    'visibility' => 'visible',
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'slug_cs' => ['type' => 'text', 'title' => 'Url'],
                    'img_src' => ['type' => 'image', 'title' => 'Obrázek'],
                    'title_cs' => ['type' => 'text', 'title' => 'Název kategorie'],
                    'meta_title_cs' => ['type' => 'text', 'title' => 'Meta title stránky'],
                    'meta_description_cs' => ['type' => 'text', 'title' => 'Meta popisek stránky']                    
                ]
            ]


        ]

    ];









    private $salesConfigArray = [

        'config' => [
            'editable' => false,
            'queryTables' => [
            ]
        ],

        'main_tables' => [ 
        
            'order_customer_info' => [
                'properties' => [
                    'type' => 'main_table',
                    'visibility' => 'visible',
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'name' => ['type' => 'text', 'title' => 'Název'],
                    'email' => ['type' => 'email', 'title' => 'Email'],
                    'phone' => ['type' => 'number', 'title' => 'Telefon'],
                    'created_at' => ['type' => 'text', 'title' => 'Datum příjetí'],
                    'total_price' => ['type' => 'number', 'title' => 'Celková cena'],

                ] 
            ],

         
           
        ],

        
    ];



    private $sessionSection;

    public function __construct()
    {
    }
    


    public function startup()
        {     
            parent::startup(); 

            $this->sessionSection = $this->getSession()->getSection('pernament-variables');
            $this->template->currentAction =  $this->getAction();
            $this->setLayout(__DIR__ . '/@layout.latte');

        }

    private function getConfigArray(){

        $table = $this->sessionSection->currentTable;
        if ($table == "products"){
            return $this->productsConfigArray;
        }elseif($table == "product_tags"){
            return $this->productCategoriesConfigArray;
        }elseif($table == "presenter_meta_data"){
            return $this->pagesConfigArray;
        }elseif($table == "orders"){
            return $this->salesConfigArray; 
        }

    }

   
   
    public function renderDefault(){

    }

    public function renderEditor(){

        $latteFilePath = __DIR__ . '\..\Home\default.latte';

        $editablePages = $this->model_admin->getEditablePages();
        $this->template->editablePages = $editablePages;

        //$this->processLatteFile($latteFilePath);
  
    }

    public function renderAccount(){

        $this->template->userLogs = $this->model_admin->getUserLogs($this->getUser()->id);
     
        $this->template->addFilter('status', 
            function ($action) { $translations = [
                'login' => 'přihlášení',
                'logout' => 'odhlášení',
            ];
            return $translations[$action] ?? $action;   
        });
    }



    public function renderProducts(){
        
        $this->template->configArray = $this->productsConfigArray;
        if (!$this->isAjax()){
            $this->sessionSection->currentTable = 'products';
            $productsArray = $this->model_admin->getItems($this->productsConfigArray);
            bdump($productsArray);
            $this->template->productsArray = $productsArray;
        }
    }


    public function renderProductCategories(){

        $this->template->configArray = $this->productCategoriesConfigArray;

        if (!$this->isAjax()){
            $this->sessionSection->currentTable = 'product_tags';
            $productsArray = $this->model_admin->getItems($this->productCategoriesConfigArray);
            $this->template->productsArray = $productsArray;
        }
    }


    public function renderPages(){

        $this->template->configArray = $this->pagesConfigArray;

        if (!$this->isAjax()){
            $this->sessionSection->currentTable = 'presenter_meta_data';
            $productsArray = $this->model_admin->getItems($this->pagesConfigArray);
            $this->template->productsArray = $productsArray;
        }
    }


    public function renderSales(){

        $this->template->configArray = $this->salesConfigArray;

        if (!$this->isAjax()){

            $this->sessionSection->currentTable = 'orders';
            $productsArray = $this->model_admin->getOrders(); 
            $this->template->productsArray = $productsArray;
        }
    }

   

    public function handleGetRowData(){
        $data = (object)$_POST;

        if($this->isAjax()){
            $this->sendJson($this->model_admin->getRowData($this->getConfigArray(), $data->item_id));
        }
    }
    


   




    public function handleSaveItem(): void
    {
    try {
        // Check if we have a file upload
        $hasFileUpload = isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK;
        bdump($hasFileUpload);
        // Initialize formData variable
        $formData = null;
        
        if ($hasFileUpload) {
            // Handle FormData with file upload
            $jsonData = isset($_POST['formData']) ? json_decode($_POST['formData'], true) : null;
            
            if ($jsonData) {

                $formData = $jsonData;
                $file = $_FILES['image_file'];
                $uploadDir = 'assets/images/products/';
                $imageFilename = null;
                
                // Search for image field in all tables
                foreach ($formData as $tableName => $tableData) {
                    foreach ($tableData as $key => $value) {
                        // If this is a string that looks like an image filename (from timestamp naming)
                        if (is_string($value) && strpos($value, '_') !== false && 
                            preg_match('/\.(jpg|jpeg|png|gif)$/i', $value)) {
                            $imageFilename = $value;
                            break 2;
                        }
                    }
                }
                
                if ($imageFilename) {
                    // Create directory if it doesn't exist
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Move the uploaded file to the destination folder
                    $destination = $uploadDir . $imageFilename;
                    move_uploaded_file($file['tmp_name'], $destination);
                }
            } else {
                throw new \Exception('Invalid data format');
            }
        } else {

            $formData =  json_decode($_POST['formData'], true);
        
        }
        
        $configArray =$this->getConfigArray();

        $this->model_admin->saveItem($configArray, $formData);
        $this->template->productsArray = $this->model_admin->getItems($configArray);

        if ($this->isAjax()) {
            $this->redrawControl('itemsList');
            $this->sendJson(['success' => true, 'message' => 'Úspěšně uloženo']);
        } else {
            $this->redirect('this');
        }
    } catch (\Exception $e) {
        if ($this->isAjax()) {
            $this->sendJson(['success' => false, 'message' => $e->getMessage()]);
            $this->terminate();
        } else {
            $this->flashMessage('Error: ' . $e->getMessage(), 'error');
            $this->redirect('this');
        }
    }
}





    public function handleDeleteItem(): void
    {
        $data = (object)$_POST;
        $this->model_admin->deleteItem($this->getConfigArray(), $data->id);
        $this->template->productsArray = $this->model_admin->getItems($this->getConfigArray());

        if ($this->isAjax()) {
            $this->redrawControl('itemsList'); 
        } else {
            $this->redirect('this');
        }
    }






    public function handleLogout(){
        $user = $this->getUser();
        $this->model_admin->logout($user->id);
        $user->logout();
        $this->redirect('Login:default');
    }




    public function handleGetContentToSave(){
        
    }


    function processLatteFile($filePath) {
        $originalContent = file_get_contents($filePath);
    
        $dom = new \DOMDocument('1.0', 'UTF-8');
    
        libxml_use_internal_errors(true);
    
        $content = '<?xml encoding="UTF-8">' . $originalContent;
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        $element = $xpath->query("//*[@save-text]")->item(0);
    
        if (!$element) {
            throw new \RuntimeException("No element with 'save-text' attribute found in the file: $filePath");
        }
    
        $savedContent = $dom->saveHTML($element);
        $savedContent = trim(str_replace("\xEF\xBB\xBF", '', $savedContent));
        $savedContent = urldecode($savedContent);
    
        if ($this->isContentProcessed($savedContent)) {
            return; 
        }
    
        $updatedContent = $this->markElementsWithKeys($savedContent);
        $updatedContent = (string)$updatedContent;
        $originalContent = (string)$originalContent;
        $savedContent = (string)$savedContent;
    
        bdump($savedContent);
        $newContent = $this->replaceContentDivDOM($originalContent, $updatedContent);
    
        file_put_contents($filePath, $newContent);
    }
    


    private function isContentProcessed($content) {

        if (preg_match('/{="\d+"\|t}/', $content)) {
            return true;
        }
        if (preg_match('/data-key="\d+"/', $content)) {
            return true;
        }
        return false;
    }

    function markElementsWithKeys($content) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        
        $content = '<?xml encoding="UTF-8">' . $content;
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
        $wrapper = $xpath->query("//*[@save-text]")->item(0);
        $textNodes = $xpath->query("./*[not(@save-text)]//text()[normalize-space()]", $wrapper);
        $keyIndex = $this->model_admin->getEditorKeyIndex();
        
        foreach ($textNodes as $textNode) {
            $text = trim($textNode->nodeValue);
            
            if (empty($text)) {
                continue;
            }
            
            $parentNode = $textNode->parentNode;
            if (in_array(strtolower($parentNode->nodeName), ['script', 'style']) || 
                $parentNode->hasAttribute('save-text')) {
                continue;
            }
            
            $textSaveArray = [
                'id' => $keyIndex,
                'cs' => $text,
            ];
            $this->model_admin->saveEditorTexts($textSaveArray);
            
            $translationNode = $dom->createTextNode("{=\"$keyIndex\"|t}");
            
            $parentNode->setAttribute('data-key', (string)$keyIndex);
            
            $textNode->parentNode->replaceChild($translationNode, $textNode);
            
            $keyIndex++;
        }
        
        $updatedContent = $dom->saveHTML($wrapper);
        
        $updatedContent = trim(str_replace("\xEF\xBB\xBF", '', $updatedContent));
        $updatedContent = urldecode($updatedContent);
        
        return $updatedContent;
    }
    
    
    function replaceContentDivDOM($originalContent, $updatedContent) {

        $parts = [];
        
        preg_match('/^({block\s+content})/', $originalContent, $blockMatch);
        $parts['block'] = $blockMatch[1] ?? '';
        
        preg_match('/{include\s+[^}]+}/', $originalContent, $includeMatch);
        $parts['include'] = $includeMatch[0] ?? '';
        
        preg_match('/({[*].*?[*]})/s', $originalContent, $commentMatch);
        $parts['comment'] = $commentMatch[0] ?? '';
        
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = true;
        $doc->formatOutput = false;
        
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $updatedContent, 
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $updatedDiv = $doc->saveHTML($doc->documentElement);
        
        $updatedDiv = str_replace([
            '<?xml encoding="UTF-8">',
            '<!DOCTYPE html>',
            '<html>',
            '</html>',
            '<body>',
            '</body>',
            "\xEF\xBB\xBF"
        ], '', $updatedDiv);
        
        $result = implode("\n", array_filter([
            $parts['block'],
            $parts['include'],
            $updatedDiv,
            $parts['comment']
        ]));
        
        $result = str_replace('%7B%24baseUrl%7D', '{$baseUrl}', $result);
        $result = str_replace('&lt;', '<', $result);
        
        return trim($result);
    }
    
    


    public function handleSaveEditorTexts(){
        $data = (array)$_POST;
        $response = $this->model_admin->updateEditorTexts($data);
        $this->sendJson($response);
    }
    

   

}
