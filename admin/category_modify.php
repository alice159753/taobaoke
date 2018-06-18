<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Category.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: category.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/category_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myCategory = new Category($myMySQL);
    $myRole = new Role($myMySQL);

    $row = $myCategory->get("*", "no = $no");
    
    $dataArray = $myCategory->getData($row);

    $myTemplate->setReplace("data", $dataArray);

    $rows = $myCategory->getRows("*", "parent_no = 0");

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = $myCategory->getData($rows[$i]);

        $myTemplate->setReplace("parent_no_lists", $dataArray, 2);
    }


    $myTemplate->process();
    $myTemplate->output();
?>