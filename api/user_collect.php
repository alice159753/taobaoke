<?php

    include_once("../config.cmd.php");

    $myProduct = new Product($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);

    $collect_type = !empty($_REQUEST["collect_type"]) ? $_REQUEST["collect_type"] : 1; 
    $user_no = !empty($_REQUEST["user_no"]) ? $_REQUEST["user_no"] : 0; 
    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : 'no desc';  
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $page_size = !empty($_REQUEST["page_size"]) ? $_REQUEST["page_size"] : 20;

    if( empty($user_no) )
    {
        Output::error('user_no 不能为空', array());
    }

    $condition = "collect_type = ". $collect_type;

    if( !empty($user_no) )
    {
        $condition .= " AND user_no = $user_no";
    }

    $total_page = $myUserCollect->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array());
    }

    $default_field = "*";
    $rows = $myUserCollect->getPage($default_field, $page, $page_size, $condition ." ORDER BY $order");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $collect_no = $rows[$i]['collect_no'];

        $productRow = $myProduct->getRow("*", "no = ". $collect_no." AND is_online = 'Y'");

        $dataArray = $myProduct->getDataClean($productRow);

        if( empty($dataArray) )
        {
            continue;
        }

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