<?php

    include_once("config.php");

    include_once("session_check.php");

    $myTemplate = new Template(TEMPLATE_DIR ."/user_config_password.html");

    include_once("common.inc.php");

    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();

?>