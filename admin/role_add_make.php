<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $permissionList = !empty($_REQUEST["permission"]) ? $_REQUEST["permission"] : array() ;

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myRole = new Role($myMySQL);

    $condition = "title = '". $title ."'";

    if( $myRole->getCount($condition) >= 1 )
    {
        JavaScript::alertAndRedirect("角色名称不能重复！", "role_add.php?r=".time());
        exit;
    }

    $dataArray = array();
    $dataArray['title']  = $title;
    $dataArray['permission'] = implode(",", $permissionList);
    $dataArray['add_time'] = 'now()';

    $myRole->addRow($dataArray);

    JavaScript::alertAndRedirect("添加成功！", "role.php?r=".time());

?>