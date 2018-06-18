<?php

    include_once("config.php");

    include_once("session_check.php");

    $myTemplate = new Template(TEMPLATE_DIR ."/user.html");

    include_once("common.inc.php");

    $myUser = new User($myMySQL);

    $row = $myUser->getRow("*", "no = ". $_SESSION['user_no']);

    $dataArray = $myUser->getData($row);

    $myTemplate->setReplace("user", $dataArray);

    $myTemplate->process();
    $myTemplate->output();


?>


