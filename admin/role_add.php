<?php

    include_once("config.php");
    include_once("permission_config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    ob_clean();

    $myTemplate = new Template(TEMPLATE_DIR ."/role_add.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    foreach ($permission_config as $key => $permission) 
    {
        $dataArray = array();
        $dataArray['{title}'] = $permission['title'];

        $myTemplate->setReplace("permission", $dataArray);

        foreach ($permission['data'] as $index => $item) 
        {
            $dataArray = array();
            $dataArray['{title}'] = $item['title'];
            $dataArray['{php}'] = $item['php'];

            $myTemplate->setReplace("list", $dataArray, 2);
        }
    }



    $myTemplate->process();
    $myTemplate->output();

?>