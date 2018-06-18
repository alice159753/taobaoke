<?php

    include_once("config.php");

    $myProduct = new Product($myMySQL);
    $myCategory = new Category($myMySQL);
    $myHotSearch = new HotSearch($myMySQL);
    $mySearch = new Search($myMySQL);

    $keyword = !empty($_REQUEST["keyword"]) ? trim($_REQUEST["keyword"]) : '';

    $condition = "title like '%".$keyword."%' AND is_online = 'Y'";
    
    $count = $myProduct->getCount($condition);

    Output::succ('提交成功',array('count' => $count));
?>