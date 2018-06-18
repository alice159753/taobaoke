<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myRole = new Role($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/admin_add.html");
    
    include_once("common.inc.php");

    $rows = $myRole->getRows("*", "1=1 ORDER BY no ASC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = array();
        $dataArray['{no}']    = $rows[$i]['no'];
        $dataArray['{title}'] = $rows[$i]['title'];

        $myTemplate->setReplace('role', $dataArray);
    }

    $myTemplate->process();
    $myTemplate->output();

?>