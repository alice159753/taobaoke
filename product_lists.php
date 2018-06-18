<?php

    include_once("config.php");

    $myProduct = new Product($myMySQL);
    $myCategory = new Category($myMySQL);

    $category_no = !empty($_REQUEST["category_no"]) ? $_REQUEST["category_no"] : 9;  //9秒杀
    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : 'price/100-coupon_save_price asc';  //综合将序

    $myTemplate = new Template(TEMPLATE_DIR ."/product_lists.html");

    include_once("common.inc.php");

    $categoryRow = $myCategory->getRow("*", "no = $category_no");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $dataArray["{category_title}"] = $categoryRow['title'];
    $dataArray["{category_no}"] = $category_no;

    //选种状态
    if( strstr($order, 'no') )
    {
        $dataArray["{no_selected}"] = 'selected';
    }

    if( strstr($order, 'sale_num') )
    {
        $dataArray["{sale_num_selected}"] = 'selected';
    }

    if( strstr($order, 'oupon_save_price') )
    {
        $dataArray["{coupon_save_price_selected}"] = 'selected';
    }

    if( strstr($order, 'price/100-coupon_save_price') )
    {
        $dataArray["{coupon_save_price_selected}"] = '';
        $dataArray["{price_real_selected}"] = 'selected';
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
    $condition = "category_no = $category_no AND is_online = 'Y'";
    
    $total_page = $myProduct->getPageCount($page_size, $condition);

    $rows = $myProduct->getPage("*", 1, $page_size, $condition ." ORDER BY $order");

    for($i = 0; isset($rows[$i]); $i++)
    {

        $dataArray = $myProduct->getData($rows[$i]);

        if( strstr($dataArray['{sale_price}'], '-') )
        {
            continue;
        }
        
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

    $data = array();
    $data['category_no'] = $category_no;

    $dataArray = array();
    $dataArray["{total_page}"] = $total_page;
    $dataArray["{data}"] = json_encode($data);

    $myTemplate->setReplace("page", $dataArray);


    $myTemplate->process();
    $myTemplate->output();









?>