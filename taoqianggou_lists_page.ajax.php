<?php

    include_once("config.php");

    $myTaoqianggou = new Taoqianggou ($myMySQL);

    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : 'no DESC';

    $myTemplate = new Template(TEMPLATE_DIR ."/taoqianggou_lists_page.ajax.html");

    include_once("common.inc.php");

    $page_size = 5;

    $condition = "1=1";

    $total_page = $myTaoqianggou->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array('html' => ''));
    }

    $rows = $myTaoqianggou->getPage("*", $page, $page_size, $condition ." ORDER BY $order");

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