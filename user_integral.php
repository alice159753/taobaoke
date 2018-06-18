<?php

    include_once("config.php");

    include_once("session_check.php");

    $myUserIntegral = new UserIntegral($myMySQL);
    $myUser = new User($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/user_integral.html");

    include_once("common.inc.php");

    $condition = "no = ". $_SESSION['user_no'];

    $userRow = $myUser->getRow("*", $condition);

    $dataArray = $myUser->getData($userRow);

    //今日获得习惯币
    $condition = "user_no = ". $_SESSION['user_no'] . " AND add_time >= '".date('Y-m-d')."' AND add_time < '".date('Y-m-d', strtotime('+1 day'))."'";
    
    $row = $myUserIntegral->getRow("sum(integral) as sum_integral", $condition);

    $dataArray['{today_integral}'] = empty($row['sum_integral']) ? 0 : $row['sum_integral'];

    $myTemplate->setReplace("integral", $dataArray);

    $page = 1;
    $page_size = 20;

    $condition = "user_no = ".$_SESSION['user_no'] . "";

    $total_count = $myUserIntegral->getCount($condition);
    $total_page = $myUserIntegral->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;
    $page = ($total_page < $page) ? $total_page : $page;

    $rows = $myUserIntegral->getPage("*", $page, $page_size,  $condition." ORDER BY no DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myUserIntegral->getData($rows[$i]);

        $myTemplate->setReplace("lists", $dataArray);
    }

    $pageArray = array();
    $pageArray['{total_page}'] = $total_page;

    $myTemplate->setReplace("page", $pageArray);

    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();

?>