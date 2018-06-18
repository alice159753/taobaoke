<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/User.php");
    include_once(INCLUDE_DIR. "/FileTools.php");
    include_once(INCLUDE_DIR. "/Password.php");
    ob_clean();

    $nickname = !empty($_REQUEST["nickname"]) ? trim($_REQUEST["nickname"]) : "" ;
    $phone = !empty($_REQUEST["phone"]) ? trim($_REQUEST["phone"]) : "" ;
    $sex = !empty($_REQUEST["sex"]) ? trim($_REQUEST["sex"]) : 0 ;
    $reg_ip = !empty($_REQUEST["reg_ip"]) ? trim($_REQUEST["reg_ip"]) : "" ;
    $password = !empty($_REQUEST["password"]) ? trim($_REQUEST["password"]) : "" ;
    $grade = !empty($_REQUEST["grade"]) ? trim($_REQUEST["grade"]) : "" ;
    $signature = !empty($_REQUEST["signature"]) ? trim($_REQUEST["signature"]) : "" ;
    $fileList = !empty($_REQUEST["fileList"]) ? trim($_REQUEST["fileList"]) : "" ;
    $email = !empty($_REQUEST["email"]) ? trim($_REQUEST["email"]) : "" ;


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myUser = new User($myMySQL);

    $condition = "phone = '". $phone ."'";

    if( $myUser->getCount($condition) >= 1 )
    {
        JavaScript::alertAndRedirect("电话不能重复！", "user_add.php?r=".time());
        exit;
    }

    $dataArray               = array();
    $dataArray['nickname']   = $nickname;
    $dataArray['phone']      = $phone;
    $dataArray['sex']        = $sex;
    $dataArray['reg_ip']     = $reg_ip;
    $dataArray['grade']      = $grade;
    $dataArray['add_time']   = 'now()';
    $dataArray['signature']  = $signature;
    $dataArray['headimgurl'] = $fileList;
    $dataArray['email']      = $email;

    $salt = Password::getSlat(32);
    $dataArray["password_salt"] = $salt;
    $dataArray["password"] = Password::encrypt($password, $salt);

    $myUser->addRow($dataArray);

    Output::succ('添加成功！', array());

?>