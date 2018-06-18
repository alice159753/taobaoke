<?php

    include_once("../config.cmd.php");

    $myProduct = new Product($myMySQL);
    $myArticle = new Article($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);

    $no = !empty($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $user_no = !empty($_REQUEST["user_no"]) ? $_REQUEST["user_no"] : 0;

    if( empty($no) )
    {
        Output::error('1', '该宝贝不存在');

    }

    $condition = "no = $no AND is_online = 'Y'";
    
    $row = $myProduct->getRow("*", $condition);

    if( empty($row) )
    {
        Output::error('1', '该宝贝已经下线');
    }

    $result = $myProduct->getDataClean($row);

    $result['sale_num'] = ceil($result['sale_num']/10000);

    //pic_lists
    $product_info = json_decode($row['product_info'], true);

    if( !empty($user_no) )
    {
        $userCollectRow = $myUserCollect->getRow("*","user_no = ".$_SESSION['user_no']." AND product_no = $no");
    }

    //判断是否收藏
    $result['is_collect'] = empty($userCollectRow) ? 'N' : 'Y';

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
        $dataArray['coupon_link'] = $row["coupon_link"];
        $dataArray['title'] = $row["title"];
        $dataArray['pic_url'] = $picLists[$i];

        $result['picLists'][] = $dataArray;
    }


    $labelList = explode(",", $row['label']);

    for($i = 0; isset($labelList[$i]); $i++)
    {
        if( empty($labelList[$i]) )
        {
            continue;
        }

        $dataArray = array(); 
        $dataArray['label'] = $labelList[$i];
        $dataArray['color'] = 'color'.($i+1);

        $result['labelLists'][] = $dataArray;
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
        $dataArray['coupon_link'] = $row["coupon_link"];
        $dataArray['title'] = $row["title"];
        $dataArray['pic_url'] = $picLists[$i];

        $result['descriptionLists'][] = $dataArray;
    }

    //like
    $condition = "is_online = 'Y' and category_no = ".$row['category_no']." ORDER BY rand() DESC LIMIT 5";
    
    $rows = $myProduct->getRows("*",  $condition);

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
        
        $result['likeLists'][] = $dataArray;
    }

    Output::succ('', $result);
    


?>