<?php

    include_once("config.php");

    $phone = !empty($_REQUEST["phone"]) ? $_REQUEST["phone"] : 0 ;
    $code = !empty($_REQUEST["code"]) ? $_REQUEST["code"] : "";
    $password1 = !empty($_REQUEST["password1"]) ? $_REQUEST["password1"] : "";
    $password2 = !empty($_REQUEST["password2"]) ? $_REQUEST["password2"] : "";

    $myUser = new User($myMySQL);

    if( empty($phone) )
    {
        JavaScript::alertAndBack("电话不能为空! ");

        exit;
    }

    $count = $myUser->getCount("phone = '".$phone."'");
    if( $count >= 1 )
    {
        JavaScript::alertAndBack("此电话已注册，请更换! ");

        exit;
    }


    //检查电话是否一致
    if( $phone != $_SESSION['register_phone'])
    {
        //JavaScript::alertAndBack("发送验证码手机号码与提交手机号码不一致!");

        //exit;
    }

    //检查发送的code是否一致
    if( $code != $_SESSION['register_code'] )
    {
        //JavaScript::alertAndBack("验证码错误! ");

        //exit;
    }

    //密码长度
    if(  strlen($password1) < 6 )
    {
        JavaScript::alertAndBack("密码至少为6位字符! ");

        exit;
    }

    //两次密码是否一致
    if( $password1 != $password2 )
    {
        JavaScript::alertAndBack("两次密码不一致! ");

        exit;
    }

    $userRow = $myUser->registerByPhone($phone, $password1);

    $myUser->login($userRow);


?>