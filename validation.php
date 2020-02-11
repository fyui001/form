<?php
class Validation {
    function __construct($contents) {
        $this->contents = $contents;
    }

    public function all() {
        return [
            'email' => $this->email($this->contents['email']),
            're_enter_email' => $this->reEnterEmail
            (
                $this->contents['email'],
                $this->contents['re_enter_email']
            ),
            'name' => !empty($this->contents['name']),
            'age' => $this->age($this->contents['age']),
            'phone_number' => $this->phoneNumber($this->contents['phone_number']),
            'type' => $this->type($this->contents['type']),
            'content' => !empty($this->contents['content'])
        ];
    }

    private function email($email) {
        if (preg_match("/^[a-zA-Z0-9_\-\.]+\@[a-zA-Z0-9_\-\.]+\.[a-zA-Z0-9]+$/", $email)) {
            return true;
        } else {
            return false;
        }

    }

    private function reEnterEmail($email, $reEnterEmail) {
        if (isset($email) && $email === $reEnterEmail) {
            return true;
        } else {
            return false;
        }
    }

    private function age($age) {
        if (empty($age) || preg_match("/^[0-9]{1,2}$/", $age)) {
            return true;
        } else {
            return false;
        }
    }

    private function phoneNumber($phoneNumber) {
        // https://www.soumu.go.jp/main_sosiki/joho_tsusin/top/tel_number/number_shitei.html
        if(empty($phoneNumber) || preg_match("/^0[0-9]{1,4}-?[0-9]{1,4}-?[0-9]{4,}$/", $phoneNumber)) {
            return true;
        } else {
            return false;
        }
    }

    private function type($type) {
        $types = [
            '料金に関するお問い合わせ',
            'サービスに関するお問い合わせ',
            'ご意見、ご要望',
            'その他お問い合わせ'
        ];

        if (in_array($type, $types)) {
            return true;
        } else {
            return false;
        }
    }
}
?>
