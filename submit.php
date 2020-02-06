<?php
class Submit {
    function __construct($gottenPost) {
        $contents = $this->adjustContents(json_decode($gottenPost, true));
        $validationStates = $this->validation($contents);

        $response = [
            "validationStates" => $validationStates,
            "contents" => $contents,
            "databaseResult" => ""
        ];

        if ($this->isValidationPassed($validationStates)) {
            $databaseResult = $this->insertDatabase($contents);

            $response["databaseResult"] = $databaseResult;
        } else {
            $response["databaseResult"] = [
                "successful" => false,
                "message" => "vaildation error",
            ];
        }

        echo json_encode($response);
    }

    private function adjustContents($contents) {
        $adjustedContents = $contents;

        // 電話番号欄が空の場合,0で埋める
        if ($adjustedContents['phoneNumber'] === "") {
            $adjustedContents['phoneNumber'] = "00000000000";
        }

        // 電話番号のハイフン除去
        if
        (
            preg_match
            (
                "/^[0-9]{2,4}-[0-9]{2,9}-[0-9]{3,4}$/",
                $adjustedContents['phoneNumber']
            )
        ) {
            str_replace(array('-', 'ー'), '', $adjustedContents['phoneNumber']);
        }


        // 年齢欄が空の場合,0歳とする
        if ($adjustedContents['age'] === "") {
            $adjustedContents['age'] = 0;
        }

        return $adjustedContents;
    }

    private function validation($contents) {
        $validationItems =  [
            "isMailTheSameAsReMail" => true,
            "isEmailAddressAvailable" => true,
            "isPhoneNumberAvailable" => true,
            "isNotTypeEmpty" => true,
            "isNotContentEmpty" => true
        ];

        // メールアドレスの確認
        // 入力と再入力が一致しない場合
        if ($contents["mail"] !== $contents["reMail"]) {
            $validationItems["isMailTheSameAsReMail"] = false;
        }

        // メールアドレスが有効ではない場合
        if
        (
            !preg_match
            (
                "/^([a-zA-Z0-9_\-\.]+)\@([a-zA-Z0-9\-\]+).\([a-zA-Z0-9]{2,20})$/",
                $contents['mail']
            )
        ) {
            $validationItems["isEmailAddressAvailable"] = false;
        }

        // 電話番号の確認
        // 電話番号が有効ではない場合
        if
        (
            !preg_match
            (
                "/^[0-9]{2,4}-?[0-9]{2,9}-?[0-9]{3,4}$/",
                $contents['phoneNumber']
            )
        ) {
            $validationItems["isPhoneNumberAvailable"] = false;
        }

        // 問い合わせ内容の確認
        // 問い合わせ内容が空の場合
        if ($contents['type'] === "") {
            $validationItems["isNotTypeEmpty"] = false;
        }

        // テキストボックス入力の確認
        // テキストボックス入力が空の場合
        if ($contents['content'] === "") {
            $validationItems["isNotContentEmpty"] = false;
        }

        return $validationItems;
    }

    private function isValidationPassed($validationItems) {
        foreach($validationItems as $_ => $state) {
            if ($state === false) {
                return false;
            }
        }

        return true;
    }

    /*
        CREATE TABLE form
        (
            id INT(11) NOT NULL AUTO_INCREMENT,
            mail VARCHAR(255) NOT NULL, re_mail VARCHAR(255) NOT NULL ,
            name VARCHAR(255) NOT NULL, age INT(11), phone_number VARCHAR(255),
            type VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )
        DEFAULT CHARSET=utf8;
    */
    private function insertDatabase($insertData) {
        $dsn = 'mysql:dbname=form; host=localhost; charset=utf8;';
        $user = 'form';
        $password = 'form';
        $tableName = 'form';

        $sql = "INSERT INTO `{$tableName}`";
        $sql .=  "(mail, re_mail, name, age, phone_number, type, content)";
        $sql .= "VALUES";
        $sql .= "(";
        // mail: VARCHAR(255)
        $sql .= "'{$insertData['mail']}',";
        $sql .= "'{$insertData['reMail']}',";
        // name: VARCHAR(255)
        $sql .= "'{$insertData['name']}',";
        // age: INT(11)
        $sql .= "{$insertData['age']},";
        // phoneNumber: VARCHAR(255)
        $sql .= "'{$insertData['phoneNumber']}',";
        // type: VARCHAR(255)
        $sql .= "'{$insertData['type']}',";
        // content: VARCHAR(255)
        $sql .= "'{$insertData['content']}'";
        $sql .= ")";

        try {
            $database = new PDO($dsn, $user, $password);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $statement = $database->prepare($sql);
            $statement->execute();

            return [
                "successful" => true,
                "errorMessage" => ""
            ];
        } catch(PDOException $error) {
            return [
                "successful" => false,
                "errorMessage" => $error->getMessage()
            ];
        }
    }

}

new Submit(file_get_contents("php://input"));
?>
