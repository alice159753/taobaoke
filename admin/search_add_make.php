<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Search.php");
    include_once(INCLUDE_DIR. "/Output.php");
    ob_clean();

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $user_no = !empty($_REQUEST["user_no"]) ? trim($_REQUEST["user_no"]) : "" ;

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySearch = new Search($myMySQL);

    $condition = "title = '". $title ."'";

    if( $mySearch->getCount($condition) >= 1 )
    {
       Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']    = $title;
    $dataArray['add_time'] = 'now()';
    $dataArray['user_no']  = $user_no;

    $mySearch->addRow($dataArray);

    Output::succ('添加成功！', array());

?>