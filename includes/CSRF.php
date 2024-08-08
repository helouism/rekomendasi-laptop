<?php


class CSRF
{   // Buat csrf token
    public static function create_token()
    {
        $token = md5(time());
        $_SESSION['token'] = $token;

        echo "<input name='token' value='$token' type= 'hidden'>";
    }

    // Validasi csrf token
    public static function validate($token)
    {
        return isset($_SESSION['token']) && $_SESSION['token'] == $token;
    }
}
