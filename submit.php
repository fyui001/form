<?php
require('items.php');
require('validation.php');

class Submit {
    function __construct($post) {
        $validation = new Validation($post);
        $validationResults = $validation->all();

        $submitResult = [
            "validation" => false,
            "database" => false
        ];

        if (!in_array(false, array_values($validationResults))) {
            $submitResult['validation'] = true;
            $databaseResult = $this->database($post);

            if ($databaseResult) {
                $submitResult['database'] = true;
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
        DEFAULT CHARSET=utf8;
    */
    private function database($insertPost) {
        $dsn = 'mysql:dbname=form; host=localhost; charset=utf8;';
        $user = 'form';
        $password = 'form';
        $tableName = 'form';

        $sql = "INSERT INTO `{$tableName}`";
        $sql .= '(email, re_enter_email, name, age, phone_number, type, content)';
        $sql .= "VALUES";
        $sql .= "(";
        // mail: VARCHAR(255)
        $sql .= "'{$insertPost['email']}',";
        $sql .= "'{$insertPost['re_enter_email']}',";
        // name: VARCHAR(255)
        $sql .= "'{$insertPost['name']}',";
        // age: INT(11)
        $sql .= "{$insertPost['age']},";
        // phoneNumber: VARCHAR(255)
        $sql .= "'{$insertPost['phone_number']}',";
        // type: VARCHAR(255)
        $sql .= "'{$insertPost['type']}',";
        // content: VARCHAR(255)
        $sql .= "'{$insertPost['content']}'";
        $sql .= ");";

        try {
            $database = new PDO($dsn, $user, $password);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $statement = $database->prepare($sql);
            $statement->execute();

            return true;
        } catch(PDOException $error) {
            return false;
        }
    }

}

new Submit(json_decode(file_get_contents('php://input'), true));
?>
