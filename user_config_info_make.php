<?php

    include_once("config.php");

    $nickname = isset($_REQUEST["nickname"]) ? $_REQUEST["nickname"] : "";
    $sex = isset($_REQUEST["sex"]) ? $_REQUEST["sex"] : '1';
    $grade = isset($_REQUEST["grade"]) ? $_REQUEST["grade"] : '';
    $signature = isset($_REQUEST["signature"]) ? $_REQUEST["signature"] : '';
    $fileList = !empty($_REQUEST["fileList"]) ? trim($_REQUEST["fileList"]) : "" ;
    $email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : '';
    $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : '';

    $myUser = new User($myMySQL);


    if( !empty($phone) )
    {
        $condition = "phone = '". $phone ."' AND no != ". $_SESSION['user_no'];

        if( $myUser->getCount($condition) >= 1 )
        {
            JavaScript::alertAndRedirect("电话已经注册了！", "user.php?r=".time());
            exit;
        }

    }

    $dataArray = array();
    $dataArray["nickname"]    = $nickname;
    $dataArray["sex"]         = $sex;
    $dataArray["grade"]       = $grade;
    $dataArray["signature"]   = $signature;
    $dataArray["update_time"] = 'now()';
    $dataArray["email"]       = $email;
    $dataArray["phone"]       = $phone;

    if( !empty($fileList) )
    {
        $dataArray['headimgurl']  = $fileList;
    }

    $myUser->update($dataArray, "no = '". $_SESSION['user_no'] ."'");

    JavaScript::alertAndRedirect("修改成功", "user.php?r=".time());


?>