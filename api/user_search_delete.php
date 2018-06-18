<?php

    include_once("../config.cmd.php");

    $mySearch = new Search($myMySQL);

    $no = !empty($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if( empty($no) )
    {
        Output::error('no 不能为空', array());
    }

    $dataArray = array();
    $dataArray['is_display'] = 0;

    $mySearch->update($dataArray, "no = $no");

    Output::succ('', array());
    


?>