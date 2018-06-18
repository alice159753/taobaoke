<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Product.php");
    ob_clean();

    $no_list = isset($_REQUEST["no_list"]) ? $_REQUEST["no_list"] : 0;
    $is_online = isset($_REQUEST["is_online"]) ? $_REQUEST["is_online"] : 'N';

    if( empty($no_list) )
    {
        Output::error('error: no not empty',array(), 1);
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myProduct = new Product($myMySQL);

    $noList = explode(",", $no_list);

    for($i = 0; isset($noList[$i]); $i++)
    {
        if( empty($noList[$i]) )
        {
            continue;
        }

        $dataArray = array();
        $dataArray['is_online'] = $is_online;

        $myProduct->update($dataArray, "no = ". $noList[$i]);
    }

    Output::succ('删除成功！',array());

?>