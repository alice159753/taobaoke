<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    ob_clean();

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myTemplate = new Template(TEMPLATE_DIR ."/article_add.html");
    
    include_once("common.inc.php");
    
    $dataArray = array();
    $dataArray['{pubdate}'] = date('Y-m-d');

    $myTemplate->setReplace("data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();

?>