<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Article.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: article.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/article_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myArticle = new Article($myMySQL);
    $myRole = new Role($myMySQL);

    $row = $myArticle->get("*", "no = $no");
    
    $dataArray = $myArticle->getData($row);

    $myTemplate->setReplace("data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();
?>