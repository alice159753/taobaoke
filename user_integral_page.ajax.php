<?php

    include_once("config.php");

    include_once("session_check.php");

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : "no desc";
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;

    $myUserIntegral = new UserIntegral($myMySQL);
    $myUser = new User($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/user_integral_page.ajax.html");

    include_once("common.inc.php");

    $page_size = 20;

    $condition = "user_no = ".$_SESSION['user_no'] . "";

    $total_count = $myUserIntegral->getCount($condition);
    $total_page = $myUserIntegral->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array('html' => ''));
    }
    
    $rows = $myUserIntegral->getPage("*", $page, $page_size,  $condition." ORDER BY no DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myUserIntegral->getData($rows[$i]);

        $myTemplate->setReplace("lists", $dataArray);
    }

    $myTemplate->process();
    $html = $myTemplate->getContent();

    Output::succ('', array('html' => $html));

?>