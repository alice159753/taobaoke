<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Article.php");
    include_once(INCLUDE_DIR. "/FileTools.php");
    include_once(INCLUDE_DIR ."/Image.php");
    ob_clean();

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $author = !empty($_REQUEST["author"]) ? $_REQUEST["author"] : '';
    $content = !empty($_REQUEST["content"]) ? $_REQUEST["content"] : '';
    $thumb_pic = !empty($_REQUEST["thumb_pic"]) ? $_REQUEST["thumb_pic"] : '';
    $pubdate = !empty($_REQUEST["pubdate"]) ? $_REQUEST["pubdate"] : date('Y-m-d');
    $is_through = !empty($_REQUEST["is_through"]) ? $_REQUEST["is_through"] : '';
    $description = !empty($_REQUEST["description"]) ? $_REQUEST["description"] : "";
    $view_count = !empty($_REQUEST["view_count"]) ? $_REQUEST["view_count"] : '0';
    $fileList = !empty($_REQUEST["fileList"]) ? trim($_REQUEST["fileList"]) : "" ;
    $pic_lists = !empty($_REQUEST["pic_lists"]) ? trim($_REQUEST["pic_lists"]) : "" ;

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myArticle = new Article($myMySQL);

    $dataArray = array();
    $dataArray['title']       = $title;
    $dataArray['author']      = $author;
    $dataArray['content']     = $content;
    $dataArray['pubdate']     = $pubdate;
    $dataArray['is_through']  = $is_through;
    $dataArray['add_time']    = 'now()';
    $dataArray['description'] = $description;
    $dataArray['view_count']  = $view_count;
    $dataArray['thumb_pic']   = $fileList;
    $dataArray['pic_lists']   = $pic_lists;

    $myArticle->addRow($dataArray);

    Output::succ('添加成功！',array());

?>