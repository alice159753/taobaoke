<?php

    include_once("config.php");

    $myProduct = new Product($myMySQL);
    $mySlideshow = new Slideshow($myMySQL);
    $myCategory = new Category($myMySQL);
    $myArticle = new Article($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/index.html");

    include_once("common.inc.php");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    //置顶
    $myTemplateCommon = new Template(TEMPLATE_DIR ."/common.html");

    $myTemplateCommon->setReplace("top", $dataArray);
    $myTemplateCommon->process();
    
    $myTemplate->setReplaceSegment("top", $myTemplateCommon->getContent());



    //轮播图, 最多展示5条
    $rows = $mySlideshow->getRows("*", "1=1 LIMIT 5");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $mySlideshow->getData($rows[$i]);

        $myTemplate->setReplace("slideshow", $dataArray);
    }

    //热门专题
    $condition = "is_through = 'Y' ORDER BY add_time LIMIT 10";
    $rows = $myArticle->getRows('*', $condition);

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myArticle->getData($rows[$i]);

        $url = $dataArray['{url}'];

        $myTemplate->setReplace("subject", $dataArray);

        $picLists = explode("##", $rows[$i]['pic_lists']);

        for($j = 0; isset($picLists[$j]); $j++)
        {
            $dataArray = array();
            $dataArray['{pic_url}'] = $picLists[$j];
            $dataArray['{url}'] = $url;

            $myTemplate->setReplace("pic", $dataArray, 2);
        }
    }

    //分类
    $rows = $myCategory->getRows("*", "is_show = 'Y' ORDER BY top DESC");

    for($i = 0; isset($rows[$i]) && $i < 8; $i++)
    {
        $dataArray = $myCategory->getData($rows[$i]);

        $myTemplate->setReplace("category_1", $dataArray);
    }

    if( count($rows) > 8 )
    {
        $myTemplate->setReplace("category_page2", array());

        for($i = 8; isset($rows[$i]) && $i < 16; $i++)
        {
            $dataArray = $myCategory->getData($rows[$i]);

            $myTemplate->setReplace("category_2", $dataArray, 2);
        }
    }

    $page_size = 5;
    $condition = "is_online = 'Y'";
    
    $total_page = $myProduct->getPageCount($page_size, $condition);

    //$rows = $myProduct->getPage("*", 1, $page_size, $condition ." ORDER BY rand()");

    $rows = $myProduct->getPage("*", 1, $page_size, $condition ." ORDER BY rand(), no DESC");

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

    $dataArray = array();
    $dataArray["{total_page}"] = $total_page;

    $myTemplate->setReplace("page", $dataArray);


    $myTemplate->process();
    $myTemplate->output();


?>