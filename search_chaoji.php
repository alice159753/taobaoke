<?php


    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Product.php");
    include_once(INCLUDE_DIR. "/Role.php");
    include_once(INCLUDE_DIR. "/Category.php");
    ob_clean();

    $keyword = !empty($_REQUEST["keyword"]) ? $_REQUEST["keyword"] : '';

    $myTemplate = new Template(TEMPLATE_DIR ."/search_chaoji.html");

    include_once("common.inc.php");

    //search
    $dataArray = array();
    $dataArray["{keyword}"] = $keyword;

    $myTemplate->setReplace("search", $dataArray);

    $myTemplate->process();
    $myTemplate->output();

?>