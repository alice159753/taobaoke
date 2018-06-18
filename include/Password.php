<?php


class Password
{
    function Password()
    {

    }

    static function getSlat($length = 16)
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);

        srand($seed);

        $result = "";

        for($i = 0; $i < $length; $i++)
        {
            $result .= chr(rand(97, 122));
        }

        return $result;
    }

    static function encrypt($password, $password_salt)
    {
        return sha1($password . $password_salt);
    }

    static function check($password, $password_salt, $encrypted_password)
    {
        return Password::encrypt($password, $password_salt) == $encrypted_password;
    }
}

?>