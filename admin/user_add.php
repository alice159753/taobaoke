<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    ob_clean();

    $myTemplate = new Template(TEMPLATE_DIR ."/user_add.html");
    
    include_once("common.inc.php");

    $myTemplate->process();
    $myTemplate->output();

?>