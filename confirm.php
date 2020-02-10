<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="reboot.css">
    <link rel="stylesheet" href="Mystyle.css">
</head>

<body>
    <header>
        <div id="header">
            <p class="title">Form</p>
        </div>
    </header>

    <main>
        <div class="form_wrapper">
            <div class="form_description">
                <h1 class="form_title">お問い合わせフォーム ご確認</h1>
            </div>

            <div id="form_content_wrapper" class="form_content_wrapper">
<?php
require('items.php');
require('validation.php');
$validation = new Validation($_POST);

$results = $validation->all();
$items = FormItems::items();

foreach($items as $item => $itemJa) {

    echo '<div class="form_content">';

    echo "<h2>".$itemJa."</h2>";

    $id = ' id='.'"'.$item.'"';
    $value = ' value='.'"'.$_POST[$item].'"';

    if ($results[$item]) {
        echo '<p'.$id.$value.'>'.$_POST[$item].'</p>';
    } else {
        echo '<p'.$id.$value.' class="required">'.$_POST[$item].'</p>';
    }

    echo "</div>";
}

if (!in_array(get_object_vars($results), false)) {
    echo '<button type="button" class="but" onClick="submit();">送信</button>';
}
?>
            </div>
        </div>
    </main>

    <footer></footer>

    <script>
        const submit = async () => {

            const contents = <?php echo json_encode($_POST); ?>

            console.log(JSON.stringify(contents));

            try {
                const response = await fetch("submit.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json;charset=utf-8"
                    },
                    mode: "same-origin",
                    body: JSON.stringify(contents)
                });
                console.log(await response.json());

                if (!response.ok) throw new Error("サーバ側のエラーにより、サーバへ送信できませんでした。");

            } catch (error) {
                alert(error);
            }
        }
    </script>
</body>

</html>
