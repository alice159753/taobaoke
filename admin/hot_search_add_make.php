<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/HotSearch.php");
    include_once(INCLUDE_DIR. "/Output.php");
    ob_clean();

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myHotSearch = new HotSearch($myMySQL);

    $condition = "title = '". $title ."'";

    if( $myHotSearch->getCount($condition) >= 1 )
    {
       Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']    = $title;
    $dataArray['add_time'] = 'now()';

    $myHotSearch->addRow($dataArray);

    Output::succ('添加成功！', array());

?>