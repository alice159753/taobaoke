<?php

    include_once("config.php");

    $myProduct = new Product($myMySQL);

    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $keyword = !empty($_REQUEST["keyword"]) ? $_REQUEST["keyword"] : '';
    $user_no = !empty($_SESSION["user_no"]) ? $_SESSION["user_no"] : 0;

    $myTemplate = new Template(TEMPLATE_DIR ."/product_search_lists_page.ajax.html");

    include_once("common.inc.php");

    $page_size = 5;

    if( empty($keyword) )
    {
        Output::succ('1', array('html' => ''));
    }

    $condition = "title like '%".$keyword."%' AND is_online = 'Y'";

    $total_page = $myProduct->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('2', array('html' => ''));
    }

    $rows = $myProduct->getPage("*", $page, $page_size, $condition ." ORDER BY add_time DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myProduct->getData($rows[$i]);

        $myTemplate->setReplace("product", $dataArray);

        if( empty($rows[$i]['coupon_link']) )
        {
            $myTemplate->setReplace("is_not_coupon", $dataArray, 2);
        }
        else
        {
            $myTemplate->setReplace("is_coupon", $dataArray, 2);
        }


        $labelList = explode(",", $rows[$i]['label']);

        for($j = 0; isset($labelList[$j]); $j++)
        {
            if( empty($labelList[$j]) )
            {
                continue;
            }

            $dataArray = array(); 
            $dataArray['{label}'] = $labelList[$j];
            $dataArray['{color}'] = 'color'.($j+1);

            $myTemplate->setReplace("label_list", $dataArray, 2);
        }
    }
 
    $myTemplate->process();
    $html = $myTemplate->getContent();

    Output::succ('', array('html' => $html));

?>