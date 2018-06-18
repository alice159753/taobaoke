<?php

    include_once("config.php");

    $myTemplate = new Template(TEMPLATE_DIR ."/category.html");

    include_once("common.inc.php");

    $myTemplate->process();
    $myTemplate->output();









?>