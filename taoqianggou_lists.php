<?php

    include_once("config.php");

    $myTaoqianggou = new Taoqianggou($myMySQL);
    $myCategory = new Category($myMySQL);

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : 'zk_final_price asc';  //综合将序

    $myTemplate = new Template(TEMPLATE_DIR ."/taoqianggou_lists.html");

    include_once("common.inc.php");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];

    //选种状态
    if( strstr($order, 'no') )
    {
        $dataArray["{no_selected}"] = 'selected';
    }

    if( strstr($order, 'total_amount') )
    {
        $dataArray["{sale_num_selected}"] = 'selected';
    }


    if( strstr($order, 'zk_final_price') )
    {
        $dataArray["{price_real_selected}"] = 'selected';
    }

    if( strstr($order, 'reserve_price - zk_final_price') )
    {
        $dataArray["{coupon_save_price_selected}"] = 'selected';
        $dataArray["{price_real_selected}"] = '';

    }


    //选种排序
    if( strstr($order, 'asc') )
    {
        $dataArray["{sort}"] = 'desc';
    }

    if( strstr($order, 'desc') )
    {
        $dataArray["{sort}"] = 'asc';
    }
    
    $myTemplate->setReplace("other_data", $dataArray);


    $page_size = 5;
    $condition = "1=1";
    
    $total_page = $myTaoqianggou->getPageCount($page_size, $condition);

    $rows = $myTaoqianggou->getPage("*", 1, $page_size, $condition ." ORDER BY $order");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myTaoqianggou->getData($rows[$i]);

        $myTemplate->setReplace("product", $dataArray);


        if( empty($rows[$i]['coupon_link']) )
        {
            $myTemplate->setReplace("is_not_coupon", $dataArray, 2);
        }
        else
        {
            $myTemplate->setReplace("is_coupon", $dataArray, 2);
        }


    }

    $data = array();
    $data['category_no'] = $category_no;

    $dataArray = array();
    $dataArray["{total_page}"] = $total_page;
    $dataArray["{data}"] = json_encode($data);

    $myTemplate->setReplace("page", $dataArray);


    $myTemplate->process();
    $myTemplate->output();









?>