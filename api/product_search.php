<?php

    include_once("../config.cmd.php");

    $myProduct = new Product($myMySQL);
    $mySearch = new Search($myMySQL);

    $keyword = !empty($_REQUEST["keyword"]) ? $_REQUEST["keyword"] : "";
    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : 'sale_num desc';  //综合将序
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $page_size = !empty($_REQUEST["page_size"]) ? $_REQUEST["page_size"] : 20;
    $user_no = !empty($_REQUEST["user_no"]) ? $_REQUEST["user_no"] : 0;

    if( empty($keyword) )
    {
        Output::succ($msg, array());
    }
    

    if( $page == 1 )
    {
        //添加搜索记录
        if( $mySearch->getCount("user_no = $user_no AND title = '$keyword'") == 0 )
        {
            $dataArray = array();
            $dataArray['user_no']   = $user_no;
            $dataArray['title']     = $keyword;
            $dataArray['add_time']  = 'now()';
            $dataArray['is_finish'] = 0;

            $mySearch->addRow($dataArray);
        }
    }


    $condition = "title like '%$keyword%' AND is_online = 'Y'";
    
    $total_page = $myProduct->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array());
    }

    $rows = $myProduct->getPage("*", $page, $page_size, $condition ." ORDER BY $order");

    if( empty($rows) )
    {
        system('cd /home/www/taobaoke/cron/; php haojuanqingdan_search.php', $out);

        exit;
    }

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