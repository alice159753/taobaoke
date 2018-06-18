<?php

    include_once("config.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myWeChat = new WeChat();
    $myUser = new User($myMySQL);

    $appid = WEIXIN_APPID;
    $appsecret = WEIXIN_APPSECRECT;
    $code = $_REQUEST['code'];

    $authorization = $myWeChat->getAuthorization($appid, $appsecret, $code);
    if( isset($authorization['errcode']) && !empty($authorization['errcode']) )
    {
        echo '登录失败请重新登录1！';
        exit;
    }

    $access_token = $authorization['access_token'];
    $openid = $authorization['openid'];
    $unionid = $authorization['unionid'];

    //判断用户是否已经存在，存在则登录，否则则创建一个新的用户
    $userRow = $myUser->getRow("*", "openid = '".$openid."'");

    if( !empty($userRow) )
    {
        $myUser->login($userRow);
        exit;
    }
    else
    {
        $access_token = $myWeChat->getToken($appid, $appsecret);

        $user = $myWeChat->getUser2($access_token, $openid);

        if( empty($user) )
        {
            echo '登录失败请重新登录2！';
            exit;
        }   

        $userRow = $myUser->register($user, 'weixin');

        $myUser->login($userRow);

    }

?>