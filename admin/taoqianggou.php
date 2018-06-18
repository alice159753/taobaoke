<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Taoqianggou.php");
    include_once(INCLUDE_DIR. "/User.php");
    ob_clean();

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : "no desc";
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $title = !empty($_REQUEST["title"]) ? $_REQUEST["title"] : "";
    $start_time_min = !empty($_REQUEST["start_time_min"]) ? $_REQUEST["start_time_min"] : "";
    $start_time_max = !empty($_REQUEST["start_time_max"]) ? $_REQUEST["start_time_max"] : "";
    $end_time_min = !empty($_REQUEST["end_time_min"]) ? $_REQUEST["end_time_min"] : "";
    $end_time_max = !empty($_REQUEST["end_time_max"]) ? $_REQUEST["end_time_max"] : "";


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myTaoqianggou = new Taoqianggou($myMySQL);
    $myUser = new User($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/taoqianggou.html");

    include_once("common.inc.php");

    // search
    $searchArray = array( "{order}" => $order );

    $searchArray['{title}']          = $title;
    $searchArray['{start_time_min}'] = $start_time_min;
    $searchArray['{start_time_max}'] = $start_time_max;
    $searchArray['{end_time_min}']   = $end_time_min;
    $searchArray['{end_time_max}']   = $end_time_max;

    $myTemplate->setReplace("search", $searchArray);

    $condition = " 1=1 ";

    if( !empty($title) )
    {
        $condition .= " AND title like '%$title%'";
    }

    if( !empty($start_time_min) )
    {
        $condition .= " AND start_time_min >= '$start_time_min'";
    }

    if( !empty($start_time_max) )
    {
        $condition .= " AND start_time_max <= '$start_time_max 23:59:59'";
    }

    if( !empty($end_time_min) )
    {
        $condition .= " AND end_time_min >= '$end_time_min'";
    }

    if( !empty($end_time_max) )
    {
        $condition .= " AND end_time_max <= '$end_time_max 23:59:59'";
    }

    

    // page
    $page_size = 50;

    $total_count = $myTaoqianggou->getCount($condition);
    $total_page = $myTaoqianggou->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;
    $page = ($total_page < $page) ? $total_page : $page;

    // list
    $rows = $myTaoqianggou->getPage("*", $page, $page_size, $condition ." ORDER BY ". str_replace("-", " ", $order));

    $random = time();

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = array();
        $dataArray["{no}"]             = $rows[$i]['no'];
        $dataArray["{category_name}"]  = $rows[$i]['category_name'];
        $dataArray["{click_url}"]      = $rows[$i]['click_url'];
        $dataArray["{start_time}"]     = $rows[$i]['start_time'];
        $dataArray["{end_time}"]       = $rows[$i]['end_time'];
        $dataArray["{num_iid}"]        = $rows[$i]['num_iid'];
        $dataArray["{pic_url}"]        = $rows[$i]['pic_url'];
        $dataArray["{reserve_price}"]  = $rows[$i]['reserve_price'];
        $dataArray["{sold_num}"]       = $rows[$i]['sold_num'];
        $dataArray["{title}"]          = $rows[$i]['title'];
        $dataArray["{total_amount}"]   = $rows[$i]['total_amount'];
        $dataArray["{zk_final_price}"] = $rows[$i]['zk_final_price'];

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