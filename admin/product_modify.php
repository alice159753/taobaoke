<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Product.php");
    include_once(INCLUDE_DIR. "/Category.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: product.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/product_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myProduct = new Product($myMySQL);
    $myCategory = new Category($myMySQL);

    $row = $myProduct->get("*", "no = $no");
    
    $dataArray = $myProduct->getData($row);

    $myTemplate->setReplace("data", $dataArray);

    $rows = $myCategory->getRows("*", "1=1");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = array();
        $dataArray['{no}'] = $rows[$i]['no'];
        $dataArray['{title}'] = $rows[$i]['title'];

        $myTemplate->setReplace("category", $dataArray, 2);
    }

    $myTemplate->process();
    $myTemplate->output();
?>