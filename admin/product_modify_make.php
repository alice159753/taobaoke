<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Product.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: product.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myProduct= new Product($myMySQL);

     // check no
    $row = $myProduct->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        Output::error('无数据',array(), 1);
    }

    unset($_REQUEST["no"]);

    $dataArray = $_REQUEST;
    $dataArray['update_time']  = 'now()';

    $dataArray['price'] = $dataArray['price'] * 100;
    $dataArray['fee'] = $dataArray['fee'] * 100;

    $myProduct->update($dataArray, "no = ". $no);

    Output::succ('修改成功',array());

?>