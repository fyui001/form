<?php
class Submit {
    function __construct($gotten_post) {

    }

    function validation($contents) {
        $validation_item =  [
            "is_required_contents_complete" => true,
            "is_mail_the_same_as_remail" => true,
            "is_available_email_address" => true,
            "is_available_phone_number" => true,
        ];

        // 必須項目の確認
        $required_contents = [
            "mail",
            "re_mail",
            "name",
            "type",
            "content"
        ];

        foreach($required_content as $required_contents) {
            if ($contents[$required_content] === "") {
                $validation_item["is_required_contents_complete"] = false;
            }
        }

        // メールアドレスの確認
        // 入力と再入力が一致しない場合
        if ($contents["mail"] !== $contents["re_mail"]) {
            $validation_item["is_mail_the_same_as_remail"] = false;
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
            $validation_item["is_available_email_address"] = false;
        }

        // 電話番号の確認
        // 電話番号が有効ではない場合
        if
        (
            !preg_match
            (
                "/^[0-9]{2,4}-?[0-9]{2,9}-?[0-9]{3,4}$/",
                $contents['phone_number']
            )
        ) {
            $validation_item["is_available_phone_number"] = false;
        }

        return $validation_item;
    }

    function insert_database($insert_data) {
        $dsn = 'mysql:dbname=form; host=localhost; charset=utf8;';
        $user = 'form';
        $password = 'form';
        $table_name = 'form';

        $is_success = true;

        try {
            $database = new PDO($dsn, $user, $password);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $sql = "INSERT INTO `{$table_name}`";
            $sql .=  "(mail, re_mail, name, age, phone_number, type, content)";
            $sql .= "VALUES ";
            $sql .= "(";
            $sql .= "{$insert_data['mail']}";
            $sql .= "{$insert_data['re_mail']}";
            $sql .= "{$insert_data['name']}";
            $sql .= "{$insert_data['age']}";
            $sql .= "{$insert_data['phone_number']}";
            $sql .= "{$insert_data['type']}";
            $sql .= "{$insert_data['content']}";
            $sql .= ")";

            $statement = $database->prepare($sql);
            $statement->execute();
        } catch(PDOException $error) {
            $is_success = false;
        }

        return $is_success;
    }

}

$my_POST = json_decode(file_get_contents("php://input"), true);
$ret = ["post"=>$my_POST, "info"=>$_SERVER];
echo json_encode($ret, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

?>
