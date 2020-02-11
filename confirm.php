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
            <p class="title">お問い合わせフォーム</p>
        </div>
    </header>

    <main>
        <div id="form_wrapper" class="form_wrapper">
            <div class="form_description">
                <h1 class="form_title">お問い合わせフォーム ご確認</h1>
            </div>

            <div id="form_content_wrapper" class="form_content_wrapper">
<?php
require('items.php');
require('validation.php');

$validation = new Validation($_POST);
$results = $validation->all();

foreach(FormItems::$items as $item => $itemJa) {
    $sanitized = htmlspecialchars($_POST[$item], ENT_QUOTES);
    $id = ' id="'.$item.'" ';
    $value = ' value="'.$sanitized.'" ';

    echo '<div class="form_content">';
    echo '<h2>'.$itemJa.'</h2>';

    if ($results[$item]) {
        echo '<p' . $id . $value . '>';
        echo  $sanitized;
        echo  '</p>';
    } else {
        echo '<p' . $id . $value . 'class="required">';
        echo $sanitized;
        echo ' は不正な値です。戻って再入力をしてください。</p>';
    }

    echo "</div>";
}

if (!in_array(false, array_values($results))) {
    echo '<button type="button" class="but" onClick="submit();">送信</button>';
}
?>
            </div>
        </div>
    </main>

    <footer></footer>

    <script>

        const writeHtml = (title, message) => {
            let page = '';
                page += '<div class="form_description">';
                page += `<h1 class="form_title">${title}</h1>`;
                page += '</div>';
                page += '<div id="form_content_wrapper" class="form_content_wrapper">';
                page += '<div class="form_content">';
                page += `<p>${message}</p>`
                page += '</div>';
                page += '</div>';

                document.getElementById("form_wrapper").innerHTML = page;
        }

        const submit = async () => {
            const contents = {
                email: document.getElementById("email").getAttribute("value"),
                re_enter_email: document.getElementById("re_enter_email").getAttribute("value"),
                name: document.getElementById("name").getAttribute("value"),
                age: document.getElementById("age").getAttribute("value"),
                phone_number: document.getElementById("phone_number").getAttribute("value"),
                type: document.getElementById("type").getAttribute("value"),
                content: document.getElementById("content").getAttribute("value")
            };

            try {
                const _response = await fetch("submit.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json;charset=utf-8"
                    },
                    mode: "same-origin",
                    body: JSON.stringify(contents)
                });

                if (!_response.ok) throw new Error("サーバ側のエラーにより、サーバへ送信できませんでした。");

                const response = await _response.json();

                console.log(response);

                if (!response.database && response.validation) {
                    throw new Error("データベースでの追加時にエラーが発生しました。");
                } else if (!response.database && !response.validation) {
                    throw new Error("不正な値が渡されました。最初からもう一度やり直してください");
                }

                writeHtml("送信完了", "送信が完了しました。後ほどご連絡いたします。")
            } catch (error) {
                writeHtml("送信失敗", error);
            }
        }
    </script>
</body>

</html>
