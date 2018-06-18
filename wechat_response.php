<?php

    include_once("config.cmd.php");


    ob_start();
    include_once(INCLUDE_DIR. "/WeChat.php");
    ob_clean();

    $token = (isset($_REQUEST["token"]) ? trim($_REQUEST["token"]) : "" );

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myWeChat = new WeChat();


    $access_token = $myWeChat->getToken(WEIXIN_APPID, WEIXIN_APPSECRECT);

    $parentMenuList = array();

    $sub = array();
    $sub['name'] = '优惠商城';
    $sub['type'] = 'view';
    $sub['url'] = 'http://shop.huaban1314.com/';
    $parentMenuList['button'][] = $sub;

    $sub = array();
    $sub['name'] = '9块9包邮';
    $sub['type'] = 'view';
    $sub['url'] = 'http://shop.huaban1314.com/product_lists.php?category_no=20';
    $parentMenuList['button'][] = $sub;


    $sub = array();
    $sub['name'] = '我的';
    $sub['type'] = 'view';
    $sub['url'] = 'http://shop.huaban1314.com/user.php';


    $parentMenuList['button'][] = $sub;

    $a = $myWeChat->makeMenu($access_token, $parentMenuList);
    print_r($a);
exit;


    $receiveArray = $myWeChat->getParseReceive();

    $from_username = $receiveArray['FromUserName'];
    $to_username = $receiveArray['ToUserName'];

    //check signature
    if( !$myWeChat->checkSignature(WEIXIN_TOKEN) )
    {
        echo $myWeChat->responseText($from_username, $to_username, "checkSignature error!");
        exit;
    }




?>