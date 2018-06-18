<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : "add_time desc";
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $title = !empty($_REQUEST["title"]) ? $_REQUEST["title"] : "";

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myRole = new Role($myMySQL);


    $myTemplate = new Template(TEMPLATE_DIR ."/role.html");

    include_once("common.inc.php");

    // search
    $searchArray = array( "{order}" => $order );

    $searchArray['{title}'] = $title;

    $myTemplate->setReplace("search", $searchArray);

    $condition = " 1=1 ";

    if ( !empty($title) )
    {
        $condition .= " AND title like  '%". $title ."%' ";
    }

    // page
    $page_size = 50;

    $total_count = $myRole->getCount($condition);
    $total_page = $myRole->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;
    $page = ($total_page < $page) ? $total_page : $page;

    // list
    $rows = $myRole->getPage("*", $page, $page_size, $condition ." ORDER BY ". str_replace("-", " ", $order));

    $random = time();

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = array();
        $dataArray["{no}"]    = $rows[$i]['no'];
        $dataArray["{title}"] = $rows[$i]['title'];
        $dataArray["{add_time}"] = $rows[$i]['add_time'];
        $dataArray["{update_time}"] = $rows[$i]['update_time'];

        $permissionList = explode(",", $rows[$i]['permission']);

        $permission_text = '';
        foreach ($permissionList as $index => $permission) 
        {
            if( empty($permission) )
            {
                continue;
            }

            $permission_text .= empty($permissionTitleList[$permission]) ? "": $permissionTitleList[$permission].", ";
        }

        $dataArray["{permission}"] = str_replace(',,', '', $permission_text);

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