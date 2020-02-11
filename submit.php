<?php
require('items.php');
require('validation.php');

class Submit {
    function __construct($post) {
        $validation = new Validation($post);
        $validationResults = $validation->all();

        $submitResult = [
            'validation' => false,
            'database' => false,
            'message' => ''
        ];

        if (!in_array(false, array_values($validationResults))) {
            $submitResult['validation'] = true;
            $databaseResult = $this->database($post);

            if ($databaseResult['state']) {
                $submitResult['database'] = true;
            } else {
                $submitResult['message'] = $databaseResult['message'];
            }
        }

        echo json_encode($submitResult);
    }

    /*
        CREATE TABLE form
        (
            id INT(11) NOT NULL AUTO_INCREMENT,
            email VARCHAR(255) NOT NULL, re_enter_email VARCHAR(255) NOT NULL ,
            name VARCHAR(255) NOT NULL, age INT(11), phone_number VARCHAR(255),
            type VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )
        DEFAULT CHARSET=utf8mb4;
    */
    private function database($post) {
        $dsn = 'mysql:dbname=form; host=localhost; charset=utf8;';
        $user = 'form';
        $password = 'form';
        $tableName = 'form';

        try {
            $database = new PDO($dsn, $user, $password);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $sql = "INSERT INTO `{$tableName}`";
            $sql .= '(email, re_enter_email, name, age, phone_number, type, content)';
            $sql .= 'VALUES';
            $sql .= "({$post['email']}, {$post['re_enter_email']}, :name, {$post['age']}, {$post['phone_number']}, {$post['type']}, :content)";

            $statement = $database->prepare($sql);
            $statement->bindValue(':name', $post['name']);
            $statement->bindValue(':content', $post['content']);
            $statement->execute();

            return [
                'state' => true,
                'message' => ''
            ];
        } catch(PDOException $error) {
            return [
                'state' => false,
                'message' => $error->getMessage()
            ];
        }
    }

}

new Submit(json_decode(file_get_contents('php://input'), true));
?>
