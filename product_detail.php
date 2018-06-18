<?php

    include_once("config.php");

    $no = !empty($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    $myTemplate = new Template(TEMPLATE_DIR ."/product_detail.html");

    include_once("common.inc.php");

    $myArticle = new Article($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);
    $myProduct = new Product($myMySQL);

    $condition = "no = $no AND is_online = 'Y'";
    
    $row = $myProduct->getRow("*", $condition);

    if( empty($row) )
    {
        JavaScript::alertAndRedirect("该宝贝已经下线", "index.php?r=".time());

        exit;
    }
    
    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $dataArray["{title}"] = $row['title'];

    $myTemplate->setReplace("other_data", $dataArray);

    //置顶
    $myTemplateCommon = new Template(TEMPLATE_DIR ."/common.html");

    $myTemplateCommon->setReplace("top", $dataArray);
    $myTemplateCommon->process();
    
    $myTemplate->setReplaceSegment("top", $myTemplateCommon->getContent());

    
    $dataArray = $myProduct->getData($row);

    //pic_lists
    $product_info = json_decode($row['product_info'], true);

    if( isset($_SESSION['user_no']) && !empty($_SESSION['user_no']) )
    {
        $userCollectRow = $myUserCollect->getRow("*","user_no = ".$_SESSION['user_no']." AND product_no = $no");
    }

    //判断是否收藏
    $dataArray['{shoucang_btn_active}'] = empty($userCollectRow) ? '' : 'active';

    $myTemplate->setReplace("product", $dataArray);

    $picLists = array();
    if( empty($product_info) )
    {
        $picLists[] = $row["pic_url"];
    }
    else
    {
        if( count($product_info['small_images']['string']) == 1 )
        {
            $picLists[] = $product_info['small_images']['string'];
        }
        else
        {
            $picLists = $product_info['small_images']['string'];
        }
    }

    for ($i = 0; isset($picLists[$i]); $i++) 
    { 
        $dataArray = array();
        $dataArray['{coupon_link}'] = $row["coupon_link"];
        $dataArray['{title}'] = $row["title"];
        $dataArray['{pic_url}'] = $picLists[$i];

        $myTemplate->setReplace("pic_lists", $dataArray, 2);
    }

    $labelList = explode(",", $row['label']);

    for($i = 0; isset($labelList[$i]); $i++)
    {
        if( empty($labelList[$i]) )
        {
            continue;
        }

        $dataArray = array(); 
        $dataArray['{label}'] = $labelList[$i];
        $dataArray['{color}'] = 'color'.($i+1);

        $myTemplate->setReplace("label_list", $dataArray, 2);
    }

    //description
    $picLists = $product_info['description'];
    for ($i=0; isset($picLists[$i]); $i++) 
    { 

        //过滤掉特殊的图片
        if( strstr($picLists[$i], 'national_emblem_light.png') || 
            strstr($picLists[$i], 'TB1XlF3RpXXXXc6XXXXXXXXXXXX-16-16.png') )
        {
            continue;
        }

        $picLists[$i] = str_replace("60x60q90", "430x430q90", $picLists[$i]);

        $dataArray = array();
        $dataArray['{coupon_link}'] = $row["coupon_link"];
        $dataArray['{title}'] = $row["title"];
        $dataArray['{pic_url}'] = $picLists[$i];

        $myTemplate->setReplace("description_lists", $dataArray, 2);
    }

    //like
    $condition = "is_online = 'Y' ORDER BY rand() DESC LIMIT 5";
    
    $rows = $myProduct->getRows("*",  $condition);

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myProduct->getData($rows[$i]);

        $myTemplate->setReplace("like", $dataArray, 2);

        if( empty($rows[$i]['coupon_link']) )
        {
            $myTemplate->setReplace("is_not_coupon", $dataArray, 3);
        }
        else
        {
            $myTemplate->setReplace("is_coupon", $dataArray, 3);
        }

    }

    //判断是否是微信
    $is_weixin = Tools::is_weixin();
    if( $is_weixin && !empty($row['coupon_command']) )
    {
        $dataArray = array();
        $dataArray['{button_title}'] = empty($row['coupon_link']) ? '淘口令购买' : '领优惠卷购买';
        $myTemplate->setReplace("is_weixin_botton", $dataArray, 2);
    }
    else
    {
        $dataArray = array();
        $dataArray['{button_title}'] = empty($row['coupon_link']) ? '淘口令购买' : '领优惠卷购买';
        $myTemplate->setReplace("is_not_weixin_botton", $dataArray, 2);
    }

 
    $myTemplate->process();
    $myTemplate->output();



?>