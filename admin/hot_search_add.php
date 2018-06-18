<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myRole = new Role($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/hot_search_add.html");
    
    include_once("common.inc.php");

    $myTemplate->process();
    $myTemplate->output();

?>