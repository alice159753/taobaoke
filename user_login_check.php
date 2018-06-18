<?php

    include_once("config.php");

    $phone    = isset($_REQUEST["phone"]) ? trim($_REQUEST["phone"]) : "";
    $password = isset($_REQUEST["password"]) ? trim($_REQUEST["password"]) : "";
   
    $myUser = new User($myMySQL);

    $userRow = $myUser->getRow("*", "phone = '". $phone ."'");
    $password_salt = $userRow['password_salt'];
    
    if( !Password::check($password, $password_salt, $userRow['password']))
    {
        JavaScript::alertAndBack("账号或者密码错误！");
        exit;
    }
    
    $myUser->setRank($userRow);

    $myUser->login($userRow);
?>