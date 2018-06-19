<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/UserFootPrint.php");
    ob_clean();

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : "no desc";
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $user_no = !empty($_REQUEST["user_no"]) ? $_REQUEST["user_no"] : "";
    $product_no = !empty($_REQUEST["product_no"]) ? $_REQUEST["product_no"] : "";

    $add_time_min = !empty($_REQUEST["add_time_min"]) ? $_REQUEST["add_time_min"] : "";
    $add_time_max = !empty($_REQUEST["add_time_max"]) ? $_REQUEST["add_time_max"] : "";


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myUserFootPrint = new UserFootPrint($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/user_footprint.html");

    include_once("common.inc.php");

    // search
    $searchArray = array( "{order}" => $order );

    $searchArray['{user_no}'] = $user_no;
    $searchArray['{add_time_max}'] = $add_time_max;
    $searchArray['{add_time_min}'] = $add_time_min;
    $searchArray['{product_no}'] = $product_no;

    $myTemplate->setReplace("search", $searchArray);

    $condition = " 1=1 ";

    if( !empty($user_no) )
    {
        $condition .= " AND user_no = $user_no";
    }

    if( !empty($product_no) )
    {
        $condition .= " AND product_no = $product_no";
    }

    if( !empty($add_time_min) )
    {
        $condition .= " AND add_time >= '$add_time_min'";
    }

    if( !empty($add_time_max) )
    {
        $condition .= " AND add_time <= '$add_time_max 23:59:59'";
    }

    // page
    $page_size = 50;

    $total_count = $myUserFootPrint->getCount($condition);
    $total_page = $myUserFootPrint->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;
    $page = ($total_page < $page) ? $total_page : $page;

    // list
    $rows = $myUserFootPrint->getPage("*", $page, $page_size, $condition ." ORDER BY ". str_replace("-", " ", $order));

    $random = time();

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = $myUserFootPrint->getData($rows[$i]);

        unset($_REQUEST['no']);
        $dataArray["{get}"] = isset($_REQUEST) ? http_build_query($_REQUEST) : "";

        $myTemplate->setReplace("list", $dataArray);
    }

    // page list
    $previous_page = $page - 1 < 1 ? 1 : $page - 1;
    $next_page = $page + 1 > $total_page ? $total_page : $page + 1;

    unset($_REQUEST["page"]);

    $dataArray = array( "{get}"           => isset($_REQUEST) ? http_build_query($_REQUEST) : "",
                        "{total_count}"   => $total_count,
                        "{total_page}"    => $total_page,
                        "{page}"          => $page,
                        "{previous_page}" => $previous_page,
                        "{next_page}"     => $next_page,
                        "{last_page}"     => $total_page );

    $myTemplate->setReplace("page_list", $dataArray);

    for($i = 4; $i > 0; $i--)
    {
        if ( $page - $i >= 1 )
        {
            $myTemplate->setReplace("previous", array("{previous}" => $page - $i), 2);
        }
    }

    for($i = 1; $i <= 4; $i++)
    {
        if ( $page + $i <= $total_page )
        {
            $myTemplate->setReplace("next", array("{next}" => $page + $i), 2);
        }
    }

    $myTemplate->process();
    $myTemplate->output();

?>