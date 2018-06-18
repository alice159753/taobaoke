<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Category.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $fileList = !empty($_REQUEST["fileList"]) ? trim($_REQUEST["fileList"]) : "" ;
    $is_show = !empty($_REQUEST["is_show"]) ? trim($_REQUEST["is_show"]) : "" ;
    $parent_no = !empty($_REQUEST["parent_no"]) ? trim($_REQUEST["parent_no"]) : "0" ;

    if ( $no == 0 )
    {
        header("Location: category.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myCategory= new Category($myMySQL);

     // check no
    $row = $myCategory->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        Output::error('无数据',array(), 1);
    }

    $condition = "title = '". $title ."' AND no != $no AND parent_no = $parent_no";

    if( $myCategory->getCount($condition) >= 1 )
    {
       Output::error('标题不能重复！',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']       = $title;
    $dataArray['update_time'] = 'now()';
    $dataArray['is_show']     = $is_show;
    $dataArray['parent_no']   = $parent_no;

    if( !empty($fileList) )
    {
        $dataArray['pic'] = $fileList;
    }

    $myCategory->update($dataArray, "no = ". $no);

    Output::succ('修改成功',array());

?>