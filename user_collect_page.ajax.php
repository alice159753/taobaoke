<?php

    include_once("config.php");

    include_once("session_check.php");

    $myProduct = new Product($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);

    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;

    $myTemplate = new Template(TEMPLATE_DIR ."/user_collect_page.ajax.html");

    include_once("common.inc.php");

    $page_size = 5;

    $condition = "user_no = ".$_SESSION['user_no'];

    $total_page = $myUserCollect->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array('html' => ''));
    }

    $rows = $myUserCollect->getPage("*", $page, $page_size, $condition ." ORDER BY add_time DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $product_no = $rows[$i]['product_no'];

        $productRow = $myProduct->getRow("*", "no = $product_no");

        $dataArray = $myProduct->getData($productRow);

        $myTemplate->setReplace("product", $dataArray);
    }
 
    $myTemplate->process();
    $html = $myTemplate->getContent();

    Output::succ('', array('html' => $html));

?>