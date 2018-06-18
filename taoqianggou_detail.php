<?php

    include_once("config.php");

    $no = !empty($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    $myTemplate = new Template(TEMPLATE_DIR ."/taoqianggou_detail.html");

    include_once("common.inc.php");

    $myArticle = new Article($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);
    $myTaoqianggou = new Taoqianggou($myMySQL);

    $condition = "no = $no";
    
    $row = $myTaoqianggou->getRow("*", $condition);

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

    $dataArray = $myTaoqianggou->getData($row);

    $myTemplate->setReplace("product", $dataArray);

    $picLists = array();
    $picLists[] = $row["pic_url"];
    
    for ($i = 0; isset($picLists[$i]); $i++) 
    { 
        $dataArray = array();
        $dataArray['{coupon_link}'] = $row["coupon_link"];
        $dataArray['{title}'] = $row["title"];
        $dataArray['{pic_url}'] = $picLists[$i];

        $myTemplate->setReplace("pic_lists", $dataArray, 2);
    }


    //like
    $condition = "1=1 ORDER BY rand() DESC LIMIT 5";
    
    $rows = $myTaoqianggou->getRows("*",  $condition);

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myTaoqianggou->getData($rows[$i]);

        $myTemplate->setReplace("like", $dataArray, 2);
    }


    //判断是否是微信
    $is_weixin = Tools::is_weixin();
    if( $is_weixin && !empty($row['click_url']) )
    {
        $dataArray = array();
        $dataArray['{button_title}'] = empty($row['click_url']) ? '淘抢购购买' : '淘抢购购买';
        $myTemplate->setReplace("is_weixin_botton", $dataArray, 2);
    }
    else
    {
        $dataArray = array();
        $dataArray['{button_title}'] = empty($row['click_url']) ? '淘抢购购买' : '淘抢购购买';
        $myTemplate->setReplace("is_not_weixin_botton", $dataArray, 2);
    }

 
    $myTemplate->process();
    $myTemplate->output();



?>