<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/HotSearch.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;

    if ( $no == 0 )
    {
        header("Location: hot_search.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myHotSearch= new HotSearch($myMySQL);

     // check no
    $row = $myHotSearch->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        Output::error('无数据',array(), 1);
    }

    $condition = "title = '". $title ."' AND no != $no";

    if( $myHotSearch->getCount($condition) >= 1 )
    {
       Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']       = $title;
    $dataArray['update_time'] = 'now()';

    $myHotSearch->update($dataArray, "no = ". $no);

    Output::succ('修改成功',array());

?>