<?php

namespace App\Core\Models;

use Nette;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\AuthenticationException;
use Nette\Database\Context;

final class Admin
{

    public      $database;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }



    public function authenticate(string $email, string $password)
    {
        $response = [
            'message' => [
                'error' => true,
                'message' => ''
            ],
            'identity' => null
        ];

        $row = $this->database->table('users')
            ->where('email', $email)
            ->fetch();

        if (!$row) {
            $response['message'] = [
                'error' => true,
                'message' => 'Neznámá emailová adresa'
            ];
            return $response;
        }

        if (!password_verify($password, $row->password_hash)) {
            $response['message'] = [
                'error' => true,
                'message' => 'Špatné přihlašovací údaje'
            ];
            return $response;
        }

        $response['message'] = [
            'error' => false,
            'message' => 'Přihlášení bylo úspěšné'
        ];

        $response['identity'] = new Identity($row->id, ['email' => $row->email]);
        $this->writeLog($row->id, 'login');
        return $response;
    }



    public function createUser(string $email, string $password): array
    {

        $emailExists = $this->database->table('users')
            ->where('email', $email)
            ->count() > 0;


        if ($emailExists) {

            return [
                'error' => true,
                'message' => 'Emailová adresa již existuje.'
            ];
        } else {

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $this->database->table('users')->insert([
                'email' => $email,
                'password_hash' => $passwordHash,
            ]);

            return [
                'error' => false,
                'message' => 'Uživatel byl úspěšně vytvořen.'
            ];
        }
    }



    public function logOut($userId)
    {
        $this->writeLog($userId, 'logout');
    }

    public function getUserLogs($userId)
    {

        $userLogs = $this->database->table('user_logs')
            ->where('user_id', $userId)
            ->fetchAll();

        return $userLogs;
    }

    private function writeLog($userId, $type)
    {

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $this->database->table('user_logs')->insert([
            'user_id' => $userId,
            'ip_adress' => $ipAddress,
            'action' => $type
        ]);
    }




    // Table editor functions





    public function getRowData($configArray, $itemId)
    {
        $result = [];
        $tables = array_keys($configArray['main_tables']);
        $mainTable = $tables[0]; // Assuming 'products' is still the first table

        // Get main table data for specific ID
        $item = $this->database->query("SELECT * FROM ?name WHERE id = ?", (string)$mainTable, $itemId)->fetch();

        if (!$item) {
            return null; // Return null if item not found
        }


        // Process child tables (now as separate entries in main_tables)
        foreach ($configArray['main_tables'] as $tableName => $tableConfig) {

            $query = "SELECT ";
            $parameters = [];

            //selection dodelat 
            if (isset($tableConfig['properties']['selection'])) {

                $query = $query . $tableConfig['properties']['selection'] . " FROM ?name WHERE ?name = ?";
            } else {
                $query = $query . "* FROM ?name WHERE ?name = ?";
            }

            $parameters[] = (string)$tableName;

            if (isset($tableConfig['properties']['where_clause']) && isset($tableConfig['properties']['reference_table'])) {
                if ($tableConfig['properties']['reference_table'] == $mainTable) {
                    $parameters[] = $tableConfig['properties']['where_clause'];
                    $parameters[] = $itemId;
                } else {
                }
            } else {
                $parameters[] = "id";
                $parameters[] = $itemId;
            }

            $result['main_tables'][$tableName] = $this->database->query($query, ...$parameters)->fetchAll();
        }

        // Get lookup tables
        $otherTables = $configArray['config']['query_tables'] ?? [];
        foreach ($otherTables as $table => $properties) {
            $selection = $properties['selection'] ?? '*';
            $result['otherTables'][$table] = $this->database->query(
                "SELECT $selection FROM ?name",
                (string)$table
            )->fetchAll();
        }

        bdump($result);
        return $result;
    }

    public function getItems($configArray)
    {
        $result = [];
        $tables = array_keys($configArray['main_tables']);
        $mainTable = $tables[0];

        // Get main table data
        $result['mainTable'] = $this->database->query("SELECT * FROM ?name", (string)$mainTable)->fetchAll();

        // Process each main table record
        foreach ($result['mainTable'] as $item) {
            $productId = $item->id;

            // Load data for child tables
            foreach ($configArray['main_tables'][$mainTable]['variables'] as $key => $value) {
                if (isset($value['type']) && $value['type'] === 'childTable') {
                    $tableName = $value['tableName'];
                    $foreignKey = $value['foreignKey'];

                    // Load child table records
                    $childRecords = $this->database->query(
                        "SELECT * FROM ?name WHERE $foreignKey = ?",
                        (string)$tableName,
                        $productId
                    )->fetchAll();

                    // Assign to the item property
                    $item->$key = (object)$childRecords;
                }
            }
        }

        // Get lookup tables
        $otherTables = $configArray['config']['queryTables'] ?? [];
        foreach ($otherTables as $table => $properties) {
            $selection = $properties['selection'] ?? '*';
            $result['otherTables'][$table] = $this->database->query(
                "SELECT $selection FROM ?name",
                (string)$table
            )->fetchAll();
        }

        return $result;
    }



    public function saveItem($configArray, $data)
    {
        bdump($data, "save Item model");
        bdump($configArray, "save Item model");

        $mainTableId = null;

        // First pass: Handle main table
        foreach ($configArray['main_tables'] as $tableName => $tableObject) {
            $variables = isset($data[$tableName]) ? $data[$tableName] : [];
            $properties = $tableObject['properties'];

            if ($properties['type'] == 'main_table') {
                // Handle main table (product) data
                if (isset($variables['id']) && $variables['id'] != '' && $variables['id'] != '-1') {
                    $id = $variables['id'];
                    unset($variables['id']);

                    // Ensure numeric fields have proper values before update
                    $this->sanitizeNumericFields($variables, $tableObject);

                    $this->database->table((string)$tableName)->where('id', $id)->update($variables);
                    $mainTableId = $id;
                } else {
                    // Prepare data for insert - handle empty strings in numeric fields
                    if (isset($variables['id'])) {
                        // Remove empty id or -1 id for new records
                        unset($variables['id']);
                    }

                    // Ensure numeric fields have proper values before insert
                    $this->sanitizeNumericFields($variables, $tableObject);

                    // Insert new record
                    $newId = $this->database->table((string)$tableName)->insert($variables);
                    $mainTableId = $newId->id;
                }
                break; // Only one main table expected
            }
        }

        // Second pass: Handle relationships and child tables
        if ($mainTableId) {
            foreach ($configArray['main_tables'] as $tableName => $tableObject) {
                $variables = isset($data[$tableName]) ? $data[$tableName] : [];
                $properties = $tableObject['properties'];

                if ($properties['type'] == 'relationships_table') {
                    // Handle relationships (tags/categories)
                    $referenceKey = $properties['where_clause'];
                    $fillTableKey = $properties['data_fill_table_reference_value'];

                    // Delete existing relationships
                    $this->database->table((string)$tableName)
                        ->where($referenceKey, $mainTableId)
                        ->delete();

                    // Insert new relationships
                    if (is_array($variables) && !empty($variables)) {
                        foreach ($variables as $tagId) {
                            $this->database->table((string)$tableName)->insert([
                                $referenceKey => (int)$mainTableId,
                                $fillTableKey => (int)$tagId,
                            ]);
                        }
                    }
                } else if ($properties['type'] == 'child_table') {
                    // Handle child table items (variations/attributes)
                    $referenceKey = $properties['where_clause'];

                    // First, delete any existing child items to avoid duplicates
                    if ($tableName === 'product_attributes') {
                        $this->database->table('product_attributes')
                            ->where('product_id', $mainTableId)
                            ->delete();
                    } else if ($tableName === 'tags_taxonomy') {
                        // For tags_taxonomy, we need to handle it differently to prevent data loss
                        // Don't delete all entries upfront; we'll handle updates individually later

                        // Extract IDs of taxonomy entries being sent from the form
                        $existingIds = [];
                        if (is_array($variables)) {
                            foreach ($variables as $row) {
                                if (
                                    isset($row['id']) && !empty($row['id']) &&
                                    !(is_string($row['id']) && strpos($row['id'], 'new-') === 0)
                                ) {
                                    $existingIds[] = (int)$row['id'];
                                }
                            }
                        }

                        bdump($existingIds, 'Existing taxonomy IDs to preserve');

                        // Only delete taxonomy entries that aren't being updated
                        if (!empty($existingIds)) {
                            $this->database->table('tags_taxonomy')
                                ->where('tag_id', $mainTableId)
                                ->where('id NOT IN ?', $existingIds)
                                ->delete();
                        } else {
                            // If we don't have any existing IDs (all entries are new), don't delete anything
                            // This prevents deleting all entries when editing a tag with just new taxonomy items
                            $existingCount = $this->database->table('tags_taxonomy')
                                ->where('tag_id', $mainTableId)
                                ->count();

                            bdump($existingCount, 'Existing taxonomy count in database');

                            if ($existingCount > 0 && count($variables) > 0) {
                                // If there are existing entries in DB and new entries from form
                                // it means we're replacing all entries
                                $this->database->table('tags_taxonomy')
                                    ->where('tag_id', $mainTableId)
                                    ->delete();
                            }
                        }
                    }

                    // For child tables, process each item
                    if (is_array($variables)) {
                        foreach ($variables as $childTableRow) {
                            // Prepare clean child table data
                            $insertData = [];

                            // For product_attributes, make sure we have the required fields
                            if ($tableName === 'product_attributes') {
                                $insertData['product_id'] = (int)$mainTableId;

                                // Map the attribute name and value from the form
                                if (isset($childTableRow['attribute_name'])) {
                                    $insertData['attribute_name'] = $childTableRow['attribute_name'];
                                }
                                if (isset($childTableRow['attribute_value'])) {
                                    $insertData['attribute_value'] = $childTableRow['attribute_value'];
                                }

                                // Debug information
                                bdump('product_attributes table data:');
                                bdump($insertData);

                                // Insert the new attribute
                                if (!empty($insertData['attribute_name']) && !empty($insertData['attribute_value'])) {
                                    $this->database->table('product_attributes')->insert($insertData);
                                }
                            } else if ($tableName === 'tags_taxonomy') {
                                // Handle tags_taxonomy with taxonomy dropdown and parent_id
                                $insertData = [
                                    'tag_id' => (int)$mainTableId
                                ];

                                // Get taxonomy value
                                if (isset($childTableRow['taxonomy'])) {
                                    $insertData['taxonomy'] = $childTableRow['taxonomy'];
                                }

                                // Handle parent_id (could be null)
                                if (isset($childTableRow['parent_id']) && $childTableRow['parent_id'] !== '') {
                                    $insertData['parent_id'] = (int)$childTableRow['parent_id'];
                                } else {
                                    $insertData['parent_id'] = null;
                                }

                                // Debug information
                                bdump('tags_taxonomy table data:');
                                bdump($insertData);

                                // Determine if this is an existing or new taxonomy entry
                                $isNewItem = !isset($childTableRow['id']) ||
                                    empty($childTableRow['id']) ||
                                    (is_string($childTableRow['id']) && strpos($childTableRow['id'], 'new-') === 0);

                                if ($isNewItem) {
                                    // Insert new taxonomy entry if we have valid data
                                    if (!empty($insertData['taxonomy'])) {
                                        $this->database->table('tags_taxonomy')->insert($insertData);
                                    }
                                } else {
                                    // Update existing taxonomy entry
                                    $rowId = (int)$childTableRow['id'];
                                    $this->database->table('tags_taxonomy')->where('id', $rowId)->update($insertData);
                                }
                            } else {
                                // Handle other child tables normally
                                // Determine if this is an existing or new child item
                                $isNewItem = !isset($childTableRow['id']) ||
                                    empty($childTableRow['id']) ||
                                    (is_string($childTableRow['id']) && strpos($childTableRow['id'], 'new-') === 0);

                                // Set the reference to parent item
                                $childTableRow[$referenceKey] = (int)$mainTableId;

                                if ($isNewItem) {
                                    // Insert new child item
                                    if (isset($childTableRow['id'])) {
                                        unset($childTableRow['id']); // Remove the temporary ID
                                    }

                                    // Ensure all numeric fields have proper values
                                    $this->sanitizeNumericFields($childTableRow, $tableObject);

                                    $this->database->table((string)$tableName)->insert($childTableRow);
                                } else {
                                    // Update existing child item
                                    $rowId = (int)$childTableRow['id'];
                                    unset($childTableRow['id']);

                                    // Ensure all numeric fields have proper values
                                    $this->sanitizeNumericFields($childTableRow, $tableObject);

                                    $this->database->table((string)$tableName)->where('id', $rowId)->update($childTableRow);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $mainTableId;
    }

    /**
     * Helper method to sanitize numeric fields before database operations
     * This prevents 'Incorrect integer value' errors in strict SQL mode
     */
    private function sanitizeNumericFields(&$data, $tableConfig)
    {
        if (!isset($tableConfig['variables']) || !is_array($tableConfig['variables'])) {
            return;
        }

        foreach ($tableConfig['variables'] as $field => $config) {
            // Handle number/integer fields
            if (isset($config['type']) && ($config['type'] === 'number' || $config['type'] === 'id')) {
                // If the field exists in our data
                if (array_key_exists($field, $data)) {
                    // If empty string, set to appropriate default
                    if ($data[$field] === '') {
                        if ($field === 'id' || strpos($field, '_id') !== false) {
                            // For ID fields, remove them so they can be auto-assigned
                            unset($data[$field]);
                        } else {
                            // For other numeric fields, use 0 as default
                            $data[$field] = 0;
                        }
                    }
                    // If it's a numeric value, ensure it's properly typed
                    else if (is_numeric($data[$field])) {
                        // Cast to integer for integer fields
                        if ($config['type'] === 'id' || strpos($field, '_id') !== false) {
                            $data[$field] = (int)$data[$field];
                        }
                        // For price, we might want to keep it as float/decimal
                        else if ($field === 'price' || strpos($field, 'price') !== false) {
                            $data[$field] = (float)$data[$field];
                        }
                        // Default to integer for other numeric fields
                        else {
                            $data[$field] = (int)$data[$field];
                        }
                    }
                }
            }
        }
    }

    private function saveTags($mainTableId, $data)
    {

        $idKey = array_key_first($data);
        $idValue = $data[$idKey];

        $categories = $data['tag_id'] ?? [];

        $this->database->table('product_relationships')
            ->where($idKey, $idValue)
            ->delete();

        foreach ($categories as $categoryId) {
            $this->database->table('product_relationships')->insert([
                $idKey => $idValue,
                'tag_id' => $categoryId,
            ]);
        }
    }




    public function deleteItem($configArray, $id)
    {
        // Make sure ID is an integer
        $id = (int)$id;

        // Get the table name from config array 
        $table = array_key_first($configArray['main_tables']);

        // Debug information
        bdump("Attempting to delete item with ID: $id from table: $table");

        try {
            // Special handling for tags (product categories)
            if ($table === 'tags') {
                // First delete any taxonomy entries for this tag
                $taxonomyCount = $this->database->table('tags_taxonomy')
                    ->where('tag_id', $id)
                    ->count();

                bdump("Found $taxonomyCount taxonomy entries to delete for tag $id");
                if ($taxonomyCount > 0) {
                    $this->database->table('tags_taxonomy')
                        ->where('tag_id', $id)
                        ->delete();
                }

                // Check if tag is used in product relationships
                $relationCount = $this->database->table('tags_relationships')
                    ->where('tag_id', $id)
                    ->count();

                bdump("Found $relationCount product relationships using tag $id");
                if ($relationCount > 0) {
                    $this->database->table('tags_relationships')
                        ->where('tag_id', $id)
                        ->delete();
                }
            }
            // Special handling for products
            else if ($table === 'products') {
                // Delete product attributes
                $this->database->table('product_attributes')
                    ->where('product_id', $id)
                    ->delete();

                // Delete product-tag relationships
                $this->database->table('tags_relationships')
                    ->where('product_id', $id)
                    ->delete();
            }

            // Now delete the main item
            $result = $this->database->table($table)
                ->where('id', $id)
                ->delete();

            bdump("Delete operation result: " . ($result ? "Success" : "No rows affected"));

            return $result;
        } catch (\Exception $e) {
            bdump("Error during delete operation: " . $e->getMessage());
            bdump("Error trace: " . $e->getTraceAsString());
            throw $e;
        }
    }



    public function saveEmail($values)
    {

        $this->database->table('emails')->insert([
            'email_type' => $values['emailType'],
            'person_name' => $values['name'],
            'person_phone' => $values['phone'],
            'person_email' => $values['email'],
            'person_message' => $values['message'],
        ]);
    }





    //Editor

    public function getEditablePages()
    {
        return $this->database->table("presenter_meta_data")->where("editable", 1)->fetchAll();
    }


    public function getEditorKeyIndex()
    {

        $row = $this->database->table('translates')
            ->order('id DESC')
            ->limit(1)
            ->fetch();

        return $row ? $row->id + 1 : 1;
    }

    public function saveEditorTexts($data)
    {
        $this->database->table('translates')->insert($data);
    }

    public function updateEditorTexts($data)
    {

        foreach ($data as $key => $value) {
            $this->database->table('translates')->where('id', $key)->update(['cs' => $value]);
        }

        $response =  [
            'error' => false,
            'message' => 'Texty úspěšně uloženy'
        ];

        return $response;
    }





    public function getOrders()
    {

        $query = "
            SELECT 
                o.id, 
                o.total_price, 
                o.created_at, 
                ci.name, 
                ci.phone,
                ci.email
            FROM orders AS o
            JOIN order_customer_info AS ci ON o.id = ci.order_id
        ";
        $orders = $this->database->fetchAll($query);
        return $orders;
    }

    public function getItemsFromTable($tableName)
    {
        // Get all items from the specified table
        $items = $this->database->query("SELECT id, title FROM ?name", (string)$tableName)->fetchAll();
        return $items;
    }
}
