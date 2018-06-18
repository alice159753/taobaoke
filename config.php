<?php

    session_start();

    mb_internal_encoding("UTF-8");

    if ( function_exists("date_default_timezone_set") )
    {
        date_default_timezone_set('Asia/Taipei');
    }

    $home_dir = dirname(__FILE__);

    define("DB_HOST", "localhost");
    define("DB_USER", 'root');
    define("DB_PASS", 'root');
    define("DB_NAME", 'taobaoke');
    define("DB_PORT", '3306');
    define("DB_PRE",  "tbk_");

    define("URL", "http://". $_SERVER["HTTP_HOST"]);
    define("HOME_DIR", $home_dir);
    define("LOGS_DIR", $home_dir."/logs");
    define("DATA_DIR", $home_dir."/data");
    define("INCLUDE_DIR", $home_dir ."/include");
    define("TEMPLATE_DIR", $home_dir ."/template");
    define("IMAGE_DIR", "/data");
    define("DOMAIN", "http://". $_SERVER["HTTP_HOST"]);
    $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER["HTTP_HOST"];
    define("URL_DIR", $url."/data");


    define("FILE_URL", "https://". $_SERVER["HTTP_HOST"]);

    define("APP", "花伴网购优惠卷");

    //微信配置
    define("WEIXIN_APPID", "wx03566870bf662cd5");
    define("WEIXIN_APPSECRECT", "e3f27168daa12f427647151910644b24");
    define("WEIXIN_TOKEN", "534299ae6e9449e0ebcc2683685d9acc");

    //导入所有的类
    $includeFileList = scandir(INCLUDE_DIR);

    ob_start();
    for($i = 0; isset($includeFileList[$i]); $i++)
    {
        if( !strstr($includeFileList[$i], '.php') )
        {
            continue;
        }

        include_once(INCLUDE_DIR ."/". $includeFileList[$i]);
    }
    ob_clean();


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);


    $whiteList = array();
    $whiteList[] = 'login.php';
    $whiteList[] = 'login_check.php';
    $whiteList[] = 'logout.php';
    $whiteList[] = 'weixin_login.php';
    $whiteList[] = 'register.php';
    $whiteList[] = 'register_make.php';
    $whiteList[] = 'test.php';
    $whiteList[] = 'setlogin.php';

    $basename = basename($_SERVER['SCRIPT_NAME']);

    if( isset($_GET['register_sign']) && !empty($_GET['register_sign']) )
    {
        $sign = urldecode($_REQUEST['register_sign']);
        $sign = base64_decode($sign);
        list($register_user_no, $register_add_time) = explode("#", $sign);
        $_SESSION['register_user_no'] = $register_user_no;
        $_SESSION['register_add_time'] = $register_add_time;
    }

    if ( !in_array($basename, $whiteList) && !isset($_SESSION['user_no']) )
    {
        //header('Location:login.php');
        //exit;
    }

    if( empty($_SESSION['user_no']) )
    {
        return '';
    }


?>