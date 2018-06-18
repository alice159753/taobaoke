<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $permissionList = !empty($_REQUEST["permission"]) ? $_REQUEST["permission"] : array() ;

    if ( $no == 0 )
    {
        header("Location: role.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myRole= new Role($myMySQL);

     // check no
    $row = $myRole->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        JavaScript::alertAndRedirect("无数据！", "role.php?r=".time());
        exit;
    }

    $condition = "title = '". $title ."' AND no != $no";
    if( $myRole->getCount($condition) >= 1 )
    {
        JavaScript::alertAndRedirect("角色名称不能重复！", "role_add.php?r=".time());
        exit;
    }

    $dataArray = array();
    $dataArray['title']  = $title;
    $dataArray['permission'] = implode(",", $permissionList);
    $dataArray['update_time'] = 'now()';

    $myRole->update($dataArray, "no = ". $no);

    //JavaScript::alertAndRedirect("修改成功!", "role.php?r=".time());

    JavaScript::alertAndBack("修改成功!",'-2');

?>