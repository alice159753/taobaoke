<?php

    include_once("config.php");

    $myProduct = new Product($myMySQL);
    $myCategory = new Category($myMySQL);
    $myHotSearch = new HotSearch($myMySQL);
    $mySearch = new Search($myMySQL);

    $keyword = !empty($_REQUEST["keyword"]) ? trim($_REQUEST["keyword"]) : '';

    $myTemplate = new Template(TEMPLATE_DIR ."/product_search_lists.html");

    include_once("common.inc.php");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);


    //获得热门搜索
    $hotSearchRows = $myHotSearch->getRows("*", "1=1");
    for($i = 0; isset($hotSearchRows[$i]); $i++)
    {
        $dataArray = $myHotSearch->getData($hotSearchRows[$i]);

        $myTemplate->setReplace("hot_search", $dataArray);
    }
    
    //search
    $dataArray = array();
    $dataArray["{keyword}"] = $keyword;

    $myTemplate->setReplace("search", $dataArray);

    if( empty($keyword) )
    {
        $myTemplate->process();
        $myTemplate->output();

        exit;
    }

    if( !empty($user_no) )
    {
        if( $mySearch->getCount("user_no = $user_no AND title = '$keyword'") == 0 )
        {
            //添加搜索记录
            $dataArray = array();
            $dataArray['user_no']   = $user_no;
            $dataArray['title']     = $keyword;
            $dataArray['add_time']  = 'now()';
            $dataArray['is_finish'] = 0;

            $mySearch->addRow($dataArray);
        }
    }

    $page_size = 5;
    $condition = "title like '%".$keyword."%' AND is_online = 'Y'";
    
    $total_page = $myProduct->getPageCount($page_size, $condition);

    $rows = $myProduct->getPage("*", 1, $page_size, $condition ." ORDER BY add_time DESC");

    if( empty($rows) )
    {
        $myTemplate->setReplace("empty", array());

        $myTemplate->process();
        $myTemplate->output();

        system('cd /home/www/taobaoke/cron/; php haojuanqingdan_search.php', $out);

        exit;
    }

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

    $data = array();
    $data['keyword'] = $keyword;

    $dataArray = array();
    $dataArray["{total_page}"] = $total_page;
    $dataArray["{data}"] = json_encode($data);

    $myTemplate->setReplace("page", $dataArray);

    $myTemplate->process();
    $myTemplate->output();









?>