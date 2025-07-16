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

        // $row = $this->database->table('users')
        //                       ->where('email', $email)
        //                       ->fetch();

        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $row = $this->database->query($query, [$email])->fetch();
        bdump(var: $row);

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


    public function getItems($configArray)
    {

        $result = (array) null;

        $tables = array_keys($configArray['mainTables']);
        $tableSelection = "";
        $params = [$tables[0], $tables[0]];
        $query = "";
        $joinQuery = "";

        for ($i = 1; $i < count($tables); $i++) {

            foreach ($configArray['mainTables'][$tables[$i]]['variables'] as $key => $value) {
                if (isset($value['joinClause'])) {
                    $joinClause = $tables[$i] . "." . $key . " = " . $value['joinClause'];
                }
                if (!isset($value['selection']) || $value['selection'] != false) {
                    $tableSelection = $tableSelection . "GROUP_CONCAT(" . $tables[$i] . "." . $key . ") AS " . $key;
                }
            }

            $joinQuery = $joinQuery . " LEFT JOIN " . $tables[$i] . " ON " . $joinClause;
        }

        if ($tableSelection == "") {
            $query = "SELECT ?name.* FROM ?name";
        } else {
            $query = "SELECT ?name.*, " . $tableSelection . " FROM ?name" . $joinQuery . " GROUP BY " . $tables[0] . ".id";
        }

        $result['mainTable'] = $this->database->query($query, ...$params)->fetchAll();


        //other tables 

        $otherTables = $configArray['config']['queryTables'];
        if (isset($otherTables)) {
            foreach ($otherTables as $table => $properties) {
                $otherTablesQuery = "SELECT " . $properties['selection'] . " FROM ?name";
                $result['otherTables'][$table] = $this->database->query($otherTablesQuery, $table)->fetchAll();
            }
        }

        return $result ?: [];
    }



    public function saveItem($data)
    {



        foreach ($data as $table => $tableValues) {

            $mainTableID = null;

            $variables = $tableValues['variables'];
            $properties = $tableValues['properties'];

            if ($properties['type'] == 'main_table') {

                $id = $variables['id'];
                unset($variables['id']);

                if (isset($id) && $id != -1) {
                    $this->database->table($table)->where('id', $id)->update($variables);
                    $mainTableID = $id;
                } else {
                    $newId = $this->database->table($table)->insert($variables)->id;
                    $mainTableID = $newId;
                }
            } else if ($properties['type'] == 'tags_table') {
                $this->saveTags($variables);
            }
        }
    }



    private function saveTags($data)
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
        bdump($configArray);
        bdump($id);
        $table = array_key_first($configArray['mainTables']);
        $this->database->table($table)
            ->where('id', $id)
            ->delete();
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
            bdump($value);
            // Check if this is an image entry
            $row = $this->database->table('translates')->get($key);

            if ($row && $row->is_image) {
                // This is an image entry
                // If the value contains the full path, extract just the relative part
                if (strpos($value, 'assets/images/') === 0) {
                    $value = substr($value, 14); // Remove 'assets/images/'
                }

                // Update just the image path, preserving the is_image flag
                $this->database->table('translates')
                    ->where('id', $key)
                    ->update(['cs' => $value]);
            } else {
                // This is a text entry
                $this->database->table('translates')
                    ->where('id', $key)
                    ->update(['cs' => $value]);
            }
        }

        $response =  [
            'error' => false,
            'message' => 'Texty úspěšně uloženy'
        ];

        return $response;
    }




    public function getEditablePages()
    {
        return $this->database->table("presenter_meta_data")->where("editable", 1)->fetchAll();
    }
}
