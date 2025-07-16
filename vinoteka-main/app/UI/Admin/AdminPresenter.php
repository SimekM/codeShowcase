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
            'query_tables' => []
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
                'tags' => ['selection' => 'id, title']
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
                    'visibility' => ['type' => 'boolean', 'title' => 'Viditelnost'],

                    'img_src' => ['type' => 'image', 'title' => 'Obrázek', 'visibility' => 'hidden'],
                    'stock' => ['type' => 'number', 'title' => 'Skladem'],
                    'price' => ['type' => 'number', 'title' => 'Cena'],
                    'title' => ['type' => 'text', 'title' => 'Název produktu'],
                    'description' => ['type' => 'text', 'title' => 'Popis produktu'],
                    'meta_title' => ['type' => 'text', 'title' => 'Meta title stránky', 'visibility' => 'hidden'],
                    'meta_description' => ['type' => 'text', 'title' => 'Meta popisek stránky', 'visibility' => 'hidden'],
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






            'product_attributes' => [
                'properties' => [
                    'title' => 'Variace produktu',
                    'type' => 'child_table',
                    'visibility' => 'hidden',
                    'reference_table' => 'products',
                    'where_clause' => 'product_id',
                    'selection' => 'id, attribute_name, attribute_value'
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'product_id' => ['type' => 'id', 'visibility' => 'hidden', 'selection' => false],
                    'attribute_name' => ['type' => 'text', 'title' => 'Název atributu'],
                    'attribute_value' => ['type' => 'text', 'title' => 'Hodnota atributu']
                ]
            ]
        ]


    ];



















    private $productCategoriesConfigArray = [

        'config' => [
            'query_tables' => [
                'tags' => ['selection' => 'id, title']
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
                    'slug' => ['type' => 'text', 'title' => 'Url'],
                    'img_src' => ['type' => 'image', 'title' => 'Obrázek', 'visibility' => 'hidden'],
                    'title' => ['type' => 'text', 'title' => 'Název kategorie'],
                    'description' => ['type' => 'text', 'title' => 'Popis kategorie'],
                    'meta_title' => ['type' => 'text', 'title' => 'Meta title stránky'],
                    'meta_description' => ['type' => 'text', 'title' => 'Meta popisek stránky']
                ]
            ],



            'tags_taxonomy' => [
                'properties' => [
                    'title' => 'Taxonomie kategorie',
                    'type' => 'child_table',
                    'visibility' => 'hidden',
                    'reference_table' => 'tags',
                    'where_clause' => 'tag_id',
                    'selection' => 'id, taxonomy, parent_id'
                ],
                'variables' => [
                    'id' => ['type' => 'id', 'title' => 'ID'],
                    'tag_id' => ['type' => 'id', 'visibility' => 'hidden', 'selection' => false],
                    'taxonomy' => ['type' => 'dropdown', 'title' => 'Typ kategorie'],
                    'parent_id' => ['type' => 'id', 'title' => 'Nadřazená kategorie']
                ]
            ]


        ]

    ];









    private $salesConfigArray = [

        'config' => [
            'editable' => false,
            'queryTables' => []
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

    public function __construct() {}



    public function startup()
    {
        parent::startup();

        $this->sessionSection = $this->getSession()->getSection('pernament-variables');
        $this->template->currentAction =  $this->getAction();
        $this->setLayout(__DIR__ . '/@layout.latte');
    }

    private function getConfigArray()
    {
        $table = $this->sessionSection->currentTable;
        bdump("Getting config array for table: $table");

        if ($table == "products") {
            return $this->productsConfigArray;
        } elseif ($table == "product_tags" || $table == "tags") {
            // Handle both 'product_tags' and 'tags' values for backward compatibility
            return $this->productCategoriesConfigArray;
        } elseif ($table == "presenter_meta_data") {
            return $this->pagesConfigArray;
        } elseif ($table == "orders") {
            return $this->salesConfigArray;
        }

        // If we're here, something is wrong with the table name
        bdump("Warning: Unknown table name: $table");

        // Return a default config as a fallback
        return $this->emptyConfigArray;
    }



    public function renderDefault() {}

    public function renderEditor()
    {

        $latteFilePath = __DIR__ . '\..\Home\default.latte';

        $editablePages = $this->model_admin->getEditablePages();
        $this->template->editablePages = $editablePages;

        //$this->processLatteFile($latteFilePath);

    }

    public function renderAccount()
    {

        $this->template->userLogs = $this->model_admin->getUserLogs($this->getUser()->id);

        $this->template->addFilter(
            'status',
            function ($action) {
                $translations = [
                    'login' => 'přihlášení',
                    'logout' => 'odhlášení',
                ];
                return $translations[$action] ?? $action;
            }
        );
    }



    public function renderProducts()
    {

        $this->template->configArray = $this->productsConfigArray;
        if (!$this->isAjax()) {
            $this->sessionSection->currentTable = 'products';
            $productsArray = $this->model_admin->getItems($this->productsConfigArray);
            bdump($productsArray);
            $this->template->productsArray = $productsArray;
        }
    }


    public function renderProductCategories()
    {
        // Set the template config array
        $this->template->configArray = $this->productCategoriesConfigArray;

        // Debug to verify the config array
        bdump($this->productCategoriesConfigArray, 'Product categories config array');

        if (!$this->isAjax()) {
            // Important: Set currentTable to 'tags' (NOT 'product_tags') to match the actual table name 
            $this->sessionSection->currentTable = 'tags';

            // Debug the set table name
            bdump("Set session table name to: " . $this->sessionSection->currentTable);

            // Get the items
            $productsArray = $this->model_admin->getItems($this->productCategoriesConfigArray);
            $this->template->productsArray = $productsArray;
        }
    }


    public function renderPages()
    {

        $this->template->configArray = $this->pagesConfigArray;

        if (!$this->isAjax()) {
            $this->sessionSection->currentTable = 'presenter_meta_data';
            $productsArray = $this->model_admin->getItems($this->pagesConfigArray);
            $this->template->productsArray = $productsArray;
        }
    }


    public function renderSales()
    {

        $this->template->configArray = $this->salesConfigArray;

        if (!$this->isAjax()) {

            $this->sessionSection->currentTable = 'orders';
            $productsArray = $this->model_admin->getOrders();
            $this->template->productsArray = $productsArray;
        }
    }



    public function handleGetRowData()
    {
        $data = (object)$_POST;

        if ($this->isAjax()) {
            // Special case for getting relationship data only (used for new items)
            if (isset($data->get_relationships) && $data->get_relationships) {
                $relationshipData = [
                    'otherTables' => []
                ];

                // Get data from the specified table
                if (isset($data->table_name)) {
                    $tableName = $data->table_name;
                    $items = $this->model_admin->getItemsFromTable($tableName);
                    $relationshipData['otherTables'][$tableName] = $items;
                }

                $this->sendJson($relationshipData);
                return;
            }

            // Regular case for getting row data for existing items
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
                            if (
                                is_string($value) && strpos($value, '_') !== false &&
                                preg_match('/\.(jpg|jpeg|png|gif)$/i', $value)
                            ) {
                                $imageFilename = $value;
                                break 2;
                            }
                        }
                    }

                    if ($imageFilename) {
                        // Create directory if it doesn't exist
                        if (!is_dir($uploadDir)) {
                            try {
                                $dirCreated = mkdir($uploadDir, 0777, true);
                                if (!$dirCreated) {
                                    bdump("Failed to create directory: $uploadDir");
                                }
                            } catch (\Exception $e) {
                                bdump("Error creating directory: " . $e->getMessage());
                            }
                        }

                        // Check if directory is writable
                        if (!is_writable($uploadDir)) {
                            bdump("Directory is not writable: $uploadDir");
                            // Try to make it writable
                            try {
                                chmod($uploadDir, 0777);
                            } catch (\Exception $e) {
                                bdump("Error changing permissions: " . $e->getMessage());
                            }
                        }

                        // Move the uploaded file to the destination folder
                        $destination = $uploadDir . $imageFilename;
                        $moveResult = move_uploaded_file($file['tmp_name'], $destination);

                        if (!$moveResult) {
                            bdump("Failed to move uploaded file to $destination");
                            bdump("Upload error code: " . $file['error']);
                            bdump("File tmp_name: " . $file['tmp_name']);
                            bdump("File exists: " . (file_exists($file['tmp_name']) ? 'Yes' : 'No'));
                        }
                    }
                } else {
                    throw new \Exception('Invalid data format');
                }
            } else {
                $formData =  json_decode($_POST['formData'], true);
            }

            $configArray = $this->getConfigArray();
            $this->model_admin->saveItem($configArray, $formData);

            // Check if we should update only the snippet
            $updateSnippet = isset($_POST['update_snippet']) && $_POST['update_snippet'] === '1';

            if ($this->isAjax()) {
                // Get fresh data for the table
                $this->template->productsArray = $this->model_admin->getItems($configArray);

                // Redraw only the items list snippet
                $this->redrawControl('itemsList');

                if (!$updateSnippet) {
                    // If not explicitly asked for snippet update, return JSON success
                    $this->sendJson(['success' => true, 'message' => 'Úspěšně uloženo']);
                }
            } else {
                $this->redirect('this');
            }
        } catch (\Exception $e) {
            bdump("Error in handleSaveItem: " . $e->getMessage());
            bdump("Error trace: " . $e->getTraceAsString());

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
        try {
            $data = $this->getHttpRequest()->getPost();
            $id = isset($data['id']) ? $data['id'] : null;

            if (!$id) {
                throw new \Exception('ID not provided');
            }

            // Debug the current session table so we know what we're working with
            bdump("Current session table: " . ($this->sessionSection->currentTable ?? 'Not set'));

            $configArray = $this->getConfigArray();
            bdump("Config array main tables: " . implode(", ", array_keys($configArray['main_tables'])));

            // Attempt to delete the item
            $deleteResult = $this->model_admin->deleteItem($configArray, $id);
            bdump("Delete operation result: " . ($deleteResult ? "Success" : "No rows affected"));

            // Check if we should update only the snippet
            $updateSnippet = isset($data['update_snippet']) && $data['update_snippet'] === '1';

            if ($this->isAjax()) {
                // Get fresh data for the table
                $this->template->productsArray = $this->model_admin->getItems($configArray);

                // Redraw only the items list snippet
                $this->redrawControl('itemsList');

                if (!$updateSnippet) {
                    // If not explicitly asked for snippet update, return JSON success
                    $this->sendJson(['success' => true, 'message' => 'Položka byla smazána']);
                }
            } else {
                $this->redirect('this');
            }
        } catch (\Exception $e) {
            bdump("Error in handleDeleteItem: " . $e->getMessage());
            bdump("Error trace: " . $e->getTraceAsString());

            if ($this->isAjax()) {
                $this->sendJson(['success' => false, 'message' => $e->getMessage()]);
                $this->terminate();
            } else {
                $this->flashMessage('Error: ' . $e->getMessage(), 'error');
                $this->redirect('this');
            }
        }
    }






    public function handleLogout()
    {
        $user = $this->getUser();
        $this->model_admin->logout($user->id);
        $user->logout();
        $this->redirect('Login:default');
    }




    public function handleGetContentToSave() {}


    function processLatteFile($filePath)
    {
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



    private function isContentProcessed($content)
    {

        if (preg_match('/{="\d+"\|t}/', $content)) {
            return true;
        }
        if (preg_match('/data-key="\d+"/', $content)) {
            return true;
        }
        return false;
    }

    function markElementsWithKeys($content)
    {
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
            if (
                in_array(strtolower($parentNode->nodeName), ['script', 'style']) ||
                ($parentNode instanceof \DOMElement && $parentNode->hasAttribute('save-text'))
            ) {
                continue;
            }

            $textSaveArray = [
                'id' => $keyIndex,
                'cs' => $text,
            ];
            $this->model_admin->saveEditorTexts($textSaveArray);

            $translationNode = $dom->createTextNode("{=\"$keyIndex\"|t}");

            if ($parentNode instanceof \DOMElement) {
                $parentNode->setAttribute('data-key', (string)$keyIndex);
            }

            $textNode->parentNode->replaceChild($translationNode, $textNode);

            $keyIndex++;
        }

        $updatedContent = $dom->saveHTML($wrapper);

        $updatedContent = trim(str_replace("\xEF\xBB\xBF", '', $updatedContent));
        $updatedContent = urldecode($updatedContent);

        return $updatedContent;
    }


    function replaceContentDivDOM($originalContent, $updatedContent)
    {

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
        $doc->loadHTML(
            '<?xml encoding="UTF-8">' . $updatedContent,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
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




    public function handleSaveEditorTexts()
    {
        $data = (array)$_POST;
        $response = $this->model_admin->updateEditorTexts($data);
        $this->sendJson($response);
    }
}
