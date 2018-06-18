<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Slideshow.php");
    ob_clean();

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $fileList = !empty($_REQUEST["fileList"]) ? trim($_REQUEST["fileList"]) : "" ;
    $url = !empty($_REQUEST["url"]) ? trim($_REQUEST["url"]) : "" ;


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySlideshow = new Slideshow($myMySQL);

    $condition = "title = '". $title ."'";

    if( $mySlideshow->getCount($condition) >= 1 )
    {
        Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']    = $title;
    $dataArray['add_time'] = 'now()';
    $dataArray['pic']      = $fileList;
    $dataArray['url']      = $url;

    $mySlideshow->addRow($dataArray);

    Output::succ('添加成功！', array());

?>