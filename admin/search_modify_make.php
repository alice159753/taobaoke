<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Search.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $user_no = !empty($_REQUEST["user_no"]) ? trim($_REQUEST["user_no"]) : "" ;

    if ( $no == 0 )
    {
        header("Location: search.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySearch= new Search($myMySQL);

     // check no
    $row = $mySearch->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        Output::error('无数据',array(), 1);
    }

    $condition = "title = '". $title ."' AND no != $no";

    if( $mySearch->getCount($condition) >= 1 )
    {
       Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']       = $title;
    $dataArray['update_time'] = 'now()';
    $dataArray['user_no']     = $user_no;

    $mySearch->update($dataArray, "no = ". $no);

    Output::succ('修改成功',array());

?>