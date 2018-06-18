<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/SystemConfig.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    $myTemplate = new Template(TEMPLATE_DIR ."/system_config_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySystemConfig = new SystemConfig($myMySQL);
    $myRole = new Role($myMySQL);

    $row = $mySystemConfig->get("*", "1 = 1 ORDER BY no DESC LIMIT 1");
    
    $dataArray = $mySystemConfig->getData($row);

    $myTemplate->setReplace("data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();
?>