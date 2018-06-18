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
    define("DB_PRE",   "tbk_");

    define("URL", "http://". $_SERVER["HTTP_HOST"]);
    define("HOME_DIR", $home_dir);
    define("LOGS_DIR", $home_dir."/logs");
    define("DATA_DIR", $home_dir."/../data/admin");
    define("INCLUDE_DIR", $home_dir ."/../include");
    define("TEMPLATE_DIR", $home_dir ."/template");
    define("IMAGE_DIR", "/data/admin");
    define("DOMAIN", "http://". $_SERVER["HTTP_HOST"]);
    $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER["HTTP_HOST"];
    define("URL_DIR", $url."/data/admin");


    ob_start();
    include_once(INCLUDE_DIR ."/MySQL.php");
    include_once(INCLUDE_DIR ."/Template.php");
    include_once(INCLUDE_DIR ."/Table.php");
    include_once(INCLUDE_DIR ."/Admin.php");
    include_once(INCLUDE_DIR ."/JavaScript.php");
    include_once(INCLUDE_DIR ."/Role.php");
    include_once(INCLUDE_DIR ."/Output.php");
    include_once(INCLUDE_DIR ."/StringFormat.php");
    include_once(INCLUDE_DIR ."/FileTools.php");
    ob_clean();

    $whiteList = array();
    $whiteList[] = 'login.php';
    $whiteList[] = 'login_check.php';
    $whiteList[] = 'logout.php';

    $basename = basename($_SERVER['SCRIPT_NAME']);

    if ( !in_array($basename, $whiteList) && !isset($_SESSION['admin_no']) )
    {
        header('Location:login.php');
        exit;
    }

    if( empty($_SESSION['admin_no']) )
    {
        return '';
    }

?>