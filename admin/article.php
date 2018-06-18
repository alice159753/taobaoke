<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Article.php");
    include_once(INCLUDE_DIR. "/Admin.php");
    ob_clean();

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : "top desc";
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $banner_no = !empty($_REQUEST["banner_no"]) ? $_REQUEST["banner_no"] : "0";
    $title = !empty($_REQUEST["title"]) ? $_REQUEST["title"] : "";
    $view_count_start = !empty($_REQUEST["view_count_start"]) ? $_REQUEST["view_count_start"] : "0";
    $view_count_end = !empty($_REQUEST["view_count_end"]) ? $_REQUEST["view_count_end"] : "0";
    $pubdate_start = !empty($_REQUEST["pubdate_start"]) ? $_REQUEST["pubdate_start"] : "";
    $pubdate_end = !empty($_REQUEST["pubdate_end"]) ? $_REQUEST["pubdate_end"] : "";
    $is_through = !empty($_REQUEST["is_through"]) ? $_REQUEST["is_through"] : "";
    $content = !empty($_REQUEST["content"]) ? $_REQUEST["content"] : "";
    $description = !empty($_REQUEST["description"]) ? $_REQUEST["description"] : "";
    $add_time_min = !empty($_REQUEST["add_time_min"]) ? $_REQUEST["add_time_min"] : "";
    $add_time_max = !empty($_REQUEST["add_time_max"]) ? $_REQUEST["add_time_max"] : "";


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myArticle = new Article($myMySQL);
    $myAdmin = new Admin($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/article.html");

    include_once("common.inc.php");

    // search
    $searchArray = array( "{order}" => $order );

    $searchArray['{banner_no}'] = !empty($_REQUEST["banner_no"]) ? 
                                  $banner_no : '';

    $searchArray['{title}'] = $title;
    $searchArray['{view_count_start}'] = !empty($_REQUEST["view_count_start"]) ? 
                                         $view_count_start : '';

    $searchArray['{view_count_end}'] = !empty($_REQUEST["view_count_end"]) ? 
                                       $view_count_end : '';

    $searchArray['{pubdate_start}'] = $pubdate_start;
    $searchArray['{pubdate_end}'] = $pubdate_end;
    $searchArray['{is_through}'] = $is_through;
    $searchArray['{content}'] = $content;
    $searchArray['{description}'] = $description;
    $searchArray['{account}'] = $account;
    $searchArray['{add_time_min}'] = $add_time_min;
    $searchArray['{add_time_max}'] = $add_time_max;

    $myTemplate->setReplace("search", $searchArray);

    $condition = " 1=1 ";


    if ( !empty($banner_no) )
    {
        $condition .= " AND banner_no =  '". $banner_no ."' ";
    }

    if ( !empty($title) )
    {
        $condition .= " AND title like  '%". $title ."%' ";
    }

    if ( !empty($view_count_start) )
    {
        $condition .= " AND view_count >=  '". $view_count_start ."' ";
    }

    if ( !empty($view_count_end) )
    {
        $condition .= " AND view_count <=  '". $view_count_end ."' ";
    }

    if ( !empty($pubdate_start) )
    {
        $condition .= " AND pubdate >=  '". $pubdate_start ."' ";
    }

    if ( !empty($pubdate_end) )
    {
        $condition .= " AND pubdate <=  '". $pubdate_end ."' ";
    }
    
    if ( !empty($is_through) )
    {
        $condition .= " AND is_through = '". $is_through ."' ";
    }

    if ( !empty($content) )
    {
        $condition .= " AND content like  '%". $content ."%' ";
    }

    if ( !empty($description) )
    {
        $condition .= " AND description like  '%". $description ."%' ";
    }

    if ( !empty($add_time_min) )
    {
        $condition .= " AND add_time >= '".$add_time_min."'";
    }

    if ( !empty($add_time_max) )
    {
        $condition .= " AND add_time <= '".$add_time_max."'";
    }

    // page
    $page_size = 50;

    $total_count = $myArticle->getCount($condition);
    $total_page = $myArticle->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;
    $page = ($total_page < $page) ? $total_page : $page;

    // list
    $rows = $myArticle->getPage("*", $page, $page_size, $condition ." ORDER BY ". str_replace("-", " ", $order));

    $random = time();

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = $myArticle->getData($rows[$i]);

        $dataArray["{url}"] = URL."/article.php?no=".$dataArray["{no}"];

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