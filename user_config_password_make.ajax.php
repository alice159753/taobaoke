<?php

    include_once("config.php");

    $password_old = isset($_REQUEST["password_old"]) ? $_REQUEST["password_old"] : "";
    $password_new1 = isset($_REQUEST["password_new1"]) ? $_REQUEST["password_new1"] : '';
    $password_new2 = isset($_REQUEST["password_new2"]) ? $_REQUEST["password_new2"] : '';

    $myUser = new User($myMySQL);

    if( empty($password_old) )
    {
        //echo '密码不能为空！';
        //exit;
    }

    if( $password_new1 != $password_new2 )
    {
        echo '两次输入的密码不一致！';
        exit;
    }

    $userRow = $myUser->getRow("*", "no = '". $_SESSION['user_no'] ."'");
    $password_salt = $userRow['password_salt'];

    if( !Password::check($password_old, $password_salt, $userRow['password']))
    {
        //echo '账号或者密码错误！';
        //exit;
    }

    $salt = Password::getSlat(32);
    $dataArray["password_salt"] = $salt;
    $dataArray["password"] = Password::encrypt($password_new1, $salt);

    $myUser->update($dataArray, "no = '". $_SESSION['user_no'] ."'");

    echo 'ok';

?>