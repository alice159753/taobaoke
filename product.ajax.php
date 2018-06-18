<?php

    include_once("config.php");

    $myProduct = new Product($myMySQL);

    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;

    $myTemplate = new Template(TEMPLATE_DIR ."/product.ajax.html");

    include_once("common.inc.php");


    $page_size = 5;

    $condition = "is_online = 'Y'";

    $total_page = $myProduct->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array('html' => ''));
    }

    $rows = $myProduct->getPage("*", $page, $page_size, $condition ." ORDER BY add_time DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myProduct->getData($rows[$i]);

        $myTemplate->setReplace("product", $dataArray);
    }
 
    $myTemplate->process();
    $html = $myTemplate->getContent();

    Output::succ('', array('html' => $html));

?>