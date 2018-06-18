<?php

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

    define("HOME_DIR", $home_dir);
    define("LOGS_DIR", $home_dir."/logs");
    define("DATA_DIR", $home_dir."/data");
    define("INCLUDE_DIR", $home_dir ."/include");
    define("TEMPLATE_DIR", $home_dir ."/template");

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

?>