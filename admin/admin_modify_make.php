<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Admin.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $account = !empty($_REQUEST["account"]) ? trim($_REQUEST["account"]) : "" ;
    $password = !empty($_REQUEST["password"]) ? trim($_REQUEST["password"]) : "" ;
    $role_no = !empty($_REQUEST["role_no"]) ? $_REQUEST["role_no"] : 0 ;

    if ( $no == 0 )
    {
        header("Location: admin.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myAdmin= new Admin($myMySQL);

     // check no
    $row = $myAdmin->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        JavaScript::alertAndRedirect("无数据！", "admin.php?r=".time());
        exit;
    }

    $condition = "account = '". $account ."'";
    $row = $myAdmin->getRow("*", $condition);

    if( !empty($row) && $row['no'] != $no )
    {
        JavaScript::alertAndRedirect("帐号不能重复！", "admin_modify.php?no=". $no);
        exit;
    }

    $dataArray = array();
    $dataArray['account']  = $account;
    $dataArray['role_no']  = $role_no;

    if( !empty($password) )
    {
        $dataArray['password'] = md5($password);
    }

    $dataArray['update_time'] = 'now()';

    $myAdmin->update($dataArray, "no = ". $no);

    //JavaScript::alertAndRedirect("修改成功!", "admin.php?r=".time());

    JavaScript::alertAndBack("修改成功!",'-2');

?>