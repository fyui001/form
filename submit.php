<?php
require('items.php');
require('validation.php');

class Submit {
    function __construct($post) {
        $validation = new Validation($post);
        $validationResults = $validation->all();

        $submitResult = [
            'validation' => false,
            'database' => false
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
            $sql .= "(:email, :re_enter_email, :name, :age, :phone_number, :type, :content)";

            $statement = $database->prepare($sql);
            $statement->bindValue(':email', $post['email'], PDO::PARAM_STR);
            $statement->bindValue(':re_enter_email', $post['re_enter_email'], PDO::PARAM_STR);
            $statement->bindValue(':name', $post['name'], PDO::PARAM_STR);
            $statement->bindValue(':age', !empty($post['age']) ? $post['age'] : 0, PDO::PARAM_INT);
            $statement->bindValue(':phone_number', $post['phone_number'], PDO::PARAM_STR);
            $statement->bindValue(':type', $post['type'], PDO::PARAM_STR);
            $statement->bindValue(':content', $post['content'], PDO::PARAM_STR);
            $statement->execute();

            return true;
        } catch(PDOException $error) {
            return true;
        }
    }

}

new Submit(json_decode(file_get_contents('php://input'), true));
?>
