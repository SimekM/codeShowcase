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

    public function __construct(Nette\Database\Explorer $database){
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

        }else{

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



    public function logOut($userId){
        $this->writeLog($userId, 'logout');
    }

    public function getUserLogs($userId){

        $userLogs = $this->database->table('user_logs')
        ->where('user_id', $userId)
        ->fetchAll();

        return $userLogs;

    }

    private function writeLog($userId, $type){

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $this->database->table('user_logs')->insert([
            'user_id' => $userId,
            'ip_adress' => $ipAddress,
            'action' => $type
        ]);
    }




    // Table editor functions





    public function getRowData($configArray, $itemId) {
        $result = [];
        $tables = array_keys($configArray['main_tables']);
        $mainTable = $tables[0]; // Assuming 'products' is still the first table
        
        // Get main table data for specific ID
        $item = $this->database->query("SELECT * FROM ?name WHERE id = ?", $mainTable, $itemId)->fetch();
        
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
            }else{
                $query = $query . "* FROM ?name WHERE ?name = ?";

            }

            $parameters[] = $tableName;

            if(isset($tableConfig['properties']['where_clause']) && isset($tableConfig['properties']['reference_table'])){
               if($tableConfig['properties']['reference_table'] == $mainTable){
                    $parameters[] = $tableConfig['properties']['where_clause'];
                    $parameters[] = $itemId;
               }else{

               }            
            }else{
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
                $table
            )->fetchAll();
        }
        
        bdump($result);
        return $result;
    }

    public function getItems($configArray) {
        $result = [];
        $tables = array_keys($configArray['main_tables']);
        $mainTable = $tables[0];
        
        // Get main table data
        $result['mainTable'] = $this->database->query("SELECT * FROM ?name", $mainTable)->fetchAll();
        
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
                        "SELECT * FROM $tableName WHERE $foreignKey = ?", 
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
                $table
            )->fetchAll();
        }

        return $result;
    }
  


    public function saveItem($configArray, $data){

        bdump($data, "save Item model");
        bdump($configArray, "save Item model");

        $mainTableId = null;

        foreach($configArray['main_tables'] as $tableName => $tableObject){


             $variables = $data[$tableName];
             $properties = $tableObject['properties'];        

             if($properties['type'] == 'main_table'){
                
                 $id = $variables['id'];
                 unset($variables['id']);

                if (isset($id) && $id != -1){
                    $this->database->table($tableName)->where('id', $id)->update($variables);
                    $mainTableId = $id;

                }else{
                    $newId = $this->database->table($tableName)->insert($variables)->id;
                    $mainTableId = $newId;
                }


            } else if($properties['type'] == 'relationships_table')
            {

                $referenceKey = $properties['where_clause'];
                $fillTableKey = $properties['data_fill_table_reference_value'];
                
                $this->database->table($tableName)
                        ->where($referenceKey, $mainTableId)
                        ->delete();

                foreach ($variables as $tagId) {
                    $this->database->table($tableName)->insert([
                        $referenceKey => $mainTableId,
                        'tag_id' => $tagId,
                    ]);
                }      

            } else if($properties['type'] == 'child_table')
            {
                $referenceKey = $properties['where_clause'];
                

                foreach ($variables as $childTableRow) {

                    $rowId = $childTableRow['id'];
                    unset($childTableRow['id']);

                    $this->database->table($tableName)->where('id', $rowId)->update($childTableRow);

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




    public function deleteItem($configArray, $id){
       
        $table = array_key_first($configArray['main_tables']);
        $this->database->table($table)
        ->where('id', $id)
        ->delete();

    }



    public function saveEmail($values){

        $this->database->table('emails')->insert([
            'email_type' => $values['emailType'],
            'person_name' => $values['name'],
            'person_phone' => $values['phone'],
            'person_email' => $values['email'],
            'person_message' => $values['message'],
        ]);

    }





    //Editor

    public function getEditablePages(){
        return $this->database->table("presenter_meta_data")->where("editable", 1)->fetchAll();
    }


    public function getEditorKeyIndex(){

        $row = $this->database->table('translates')
            ->order('id DESC')
            ->limit(1)
            ->fetch();

        return $row ? $row->id + 1 : 1;
    }

    public function saveEditorTexts($data){
        $this->database->table('translates')->insert($data);
    }

    public function updateEditorTexts($data){

        foreach($data as $key => $value){
            $this->database->table('translates')->where('id', $key)->update(['cs' => $value]);
        }

        $response =  [
            'error' => false,
            'message' => 'Texty úspěšně uloženy'
        ];

        return $response;
    }


    


    public function getOrders(){

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




}
   


