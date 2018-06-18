<?php

    include_once("config.php");

    $myTemplate = new Template(TEMPLATE_DIR ."/user_register.html");

    include_once("common.inc.php");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    
    $myTemplate->process();
    $myTemplate->output();


?>


