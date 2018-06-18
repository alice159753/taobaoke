<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/User.php");
    ob_clean();

    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $_REQUEST["no"] == 0 )
    {
        Output::error('无数据！',array(), 1);
        exit;
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myUser = new User($myMySQL);

    $myUser->remove("no = ". $no);

    Output::succ('删除成功！',array());

?>