<?php 


    if( empty($_SESSION['user_no']) )
    {
        if( Tools::is_weixin() )
        {
            $myWeChat = new WeChat();

            $myWeChat->login(WEIXIN_APPID, URL.'/wechat_login.php');
            exit;
        }
        else
        {
            header('Location:user_login.php');
            exit;
        }
    }



?>