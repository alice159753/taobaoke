<?php

    include_once("config.php");

    //include_once("session_check.php");

    $content = !empty($_REQUEST["content"]) ? trim($_REQUEST["content"]) : "";

    if( empty($content) )
    {
        Output::error('建议内容不能为空！',array(), 1);
    }

    $mySuggest = new Suggest($myMySQL);

    $dataArray = array();
    $dataArray['user_no']  = $_SESSION['user_no'];
    $dataArray['content']  = $content;
    $dataArray['add_time'] = 'now()';

    $mySuggest->addRow($dataArray);

    Output::succ('提交成功',array());

?>