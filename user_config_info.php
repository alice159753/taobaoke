<?php

    include_once("config.php");

    include_once("session_check.php");

    $myUser = new User($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/user_config_info.html");

    include_once("common.inc.php");

    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    //获得用户的信息
    $userRow = $myUser->getRow("*", "no = ". $_SESSION['user_no']);
    $dataArray = $myUser->getData($userRow, false);
 
    if( empty($userRow['signature']) )
    {
        $dataArray['{signature}'] = "";
    }

    $myTemplate->setReplace("user", $dataArray);


    $myTemplate->process();
    $myTemplate->output();

?>