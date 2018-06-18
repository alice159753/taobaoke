<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Category.php");
    ob_clean();

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myCategory = new Category($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/product_add.html");
    
    include_once("common.inc.php");

    $rows = $myCategory->getRows("*", "1=1");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = array();
        $dataArray['{no}'] = $rows[$i]['no'];
        $dataArray['{title}'] = $rows[$i]['title'];

        $myTemplate->setReplace("category", $dataArray);

    }

    $myTemplate->process();
    $myTemplate->output();

?>