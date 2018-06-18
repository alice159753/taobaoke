<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Category.php");
    include_once(INCLUDE_DIR. "/Output.php");
    ob_clean();

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $fileList = !empty($_REQUEST["fileList"]) ? trim($_REQUEST["fileList"]) : "" ;
    $is_show = !empty($_REQUEST["is_show"]) ? trim($_REQUEST["is_show"]) : "" ;
    $parent_no = !empty($_REQUEST["parent_no"]) ? trim($_REQUEST["parent_no"]) : "0" ;

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myCategory = new Category($myMySQL);

    $condition = "title = '". $title ."' AND parent_no = $parent_no";

    if( $myCategory->getCount($condition) >= 1 )
    {
       Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']    = $title;
    $dataArray['add_time'] = 'now()';
    $dataArray['pic']      = $fileList;
    $dataArray['is_show']  = $is_show;
    $dataArray['parent_no']= $parent_no;

    $myCategory->addRow($dataArray);

    Output::succ('添加成功！', array());

?>