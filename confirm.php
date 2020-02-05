<?php

/**
 * ajax通信のリクエストか判定する
 *
 * @return bool
 */
function isAjax() {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    } else {
        return false;
    }
}

/**
 * リクエストされたお問い合わせの内容のバリデーション
 *
 * @param $data56i
 * @return array
 */
function validation($data){
    $requestLen = lenght($data);
    $requierList = [
        'mail',
        're_mail',
        'name',
        'type',
        'content'
    ];

    for ($i=0; $i < ($requestLen-1); $i++) {
        if ( !$data[$requestLen[$i]] ) {
            return [
                'status' => false,
                'msg' => '必須項目が入力されていません'
            ];
        }
    }

    /* メールアドレスの整合性確認 */
    if ($data['mail'] !== $data['re_mail']) {
        return [
            'status' => false,
            'msg' => 'メールアドレスが一致しません'
        ];
    } elseif ( !preg_match("/^([a-zA-Z0-9_\-\.]+)\@([a-zA-Z0-9\-\]+).\([a-zA-Z0-9]{2,20})$/", $data['mail'])){
        return [
            'status' => false,
            'msg' => 'このメールアドレスは使用できません'
        ];
    }

    /* 電話番号の形式チェック */
    if(preg_match("/^[0-9]{2,4}-[0-9]{2,9}-[0-9]{3,4}$/", $data['phone_num'])){
        str_replace(array('-', 'ー'), '', $data['phone_num']);
    } elseif (!preg_match("/^[0-9]{10,11}$/",$data['phone_num'])) {
        return [
            'status' => false,
            'msg' => 'この電話番号は使用できません'
        ];
    }

    return [
        'status' => true,
        'data' => $data
    ];
}

if (!isAjax()) {
    header('location:index.php');
} else {
    /*
        バリデーション処理の例はこんな感じ、リクエストを受けたらまずそのデータの整合性を確認する。
        [プロトコル設計（例）]
        サーバー側は
        ・バリデーションに成功したかどうか(必須)
        ・フロント側への通知メッセージ(あると良い)
        ・フロント側へのデータ転送(phpファイルでもこれはhtmlでデータの表示ができるので好きな奴をいい感じによろしく)
    */
    validation($_POST);
}
