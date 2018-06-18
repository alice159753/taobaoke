<?php

    include_once("../config.cmd.php");

    $myProduct = new Product($myMySQL);

    $category_no = !empty($_REQUEST["category_no"]) ? $_REQUEST["category_no"] : 0;  //9秒杀
    $category_no1 = !empty($_REQUEST["category_no1"]) ? $_REQUEST["category_no1"] : 0;  //一级分类
    $category_no2 = !empty($_REQUEST["category_no2"]) ? $_REQUEST["category_no2"] : 0;  //二级分类
    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : 'sale_num desc';  //综合将序
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $page_size = !empty($_REQUEST["page_size"]) ? $_REQUEST["page_size"] : 20;

    $condition = "is_online = 'Y'";

    if( !empty($category_no) )
    {
        $condition .= " AND category_no = $category_no";
    }

    if( !empty($category_no1) )
    {
        $condition .= " AND category_no1 = $category_no1";
    }

    if( !empty($category_no) )
    {
        $condition .= " AND category_no2 = $category_no2";
    }


    $total_page = $myProduct->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array());
    }

    $default_field = "*";
    $rows = $myProduct->getPage($default_field, $page, $page_size, $condition ." ORDER BY $order");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myProduct->getDataClean($rows[$i]);

        if( strstr($dataArray['sale_price'], '-') )
        {
            continue;
        }

        if( $dataArray['price'] == 0 )
        {
            continue;
        }
        
        $result[] = $dataArray;
    }

    Output::succ('', $result);
    


?>