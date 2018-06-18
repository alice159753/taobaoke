<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/OpenUser.php");
    include_once(INCLUDE_DIR. "/User.php");
    ob_clean();

    $order = !empty($_REQUEST["order"]) ? $_REQUEST["order"] : "no desc";
    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
    $nickname = !empty($_REQUEST["nickname"]) ? $_REQUEST["nickname"] : "";
    $openid = !empty($_REQUEST["openid"]) ? $_REQUEST["openid"] : "";
    $unionid = !empty($_REQUEST["unionid"]) ? $_REQUEST["unionid"] : "";
    $add_time_min = !empty($_REQUEST["add_time_min"]) ? $_REQUEST["add_time_min"] : "";
    $add_time_max = !empty($_REQUEST["add_time_max"]) ? $_REQUEST["add_time_max"] : "";
    $user_name = !empty($_REQUEST["user_name"]) ? $_REQUEST["user_name"] : "";
    $user_no = !empty($_REQUEST["user_no"]) ? $_REQUEST["user_no"] : "";


    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myOpenUser = new OpenUser($myMySQL);
    $myUser = new User($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/open_user.html");

    include_once("common.inc.php");

    // search
    $searchArray = array( "{order}" => $order );

    $searchArray['{nickname}']     = $nickname;
    $searchArray['{openid}']       = $openid;
    $searchArray['{add_time_max}'] = $add_time_max;
    $searchArray['{add_time_min}'] = $add_time_min;
    $searchArray['{user_no}']      = $user_no;
    $searchArray['{user_name}']    = $user_name;
    $searchArray['{unionid}']      = $unionid;

    $myTemplate->setReplace("search", $searchArray);

    $condition = " 1=1 ";

    if( !empty($nickname) )
    {
        $condition .= " AND nickname like '%$nickname%'";
    }

    if( !empty($openid) )
    {
        $condition .= " AND openid like '%$openid%'";
    }

    if( !empty($unionid) )
    {
        $condition .= " AND unionid = '$unionid'";
    }

    if( !empty($add_time_min) )
    {
        $condition .= " AND add_time >= '$add_time_min'";
    }

    if( !empty($add_time_max) )
    {
        $condition .= " AND add_time <= '$add_time_max 23:59:59'";
    }

    if ( !empty($user_no) )
    {
        $condition .= " AND user_no = $user_no ";
    }

    if ( !empty($user_name) )
    {
        $rows = $myUser->getRows("*", "nickname like '%".$user_name."%'");   
        if( !empty($rows) )
        {  
            $ids = array();
            for($i = 0; isset($rows[$i]); $i++)
            {
                $ids[] = $rows[$i]['no'];
            }

            $ids = implode(",", $ids);
            $condition .= " AND user_no in($ids) ";
        }
        else
        {
            $condition .= " AND user_no in(0) ";
        }
    }

    // page
    $page_size = 50;

    $total_count = $myOpenUser->getCount($condition);
    $total_page = $myOpenUser->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;
    $page = ($total_page < $page) ? $total_page : $page;

    // list
    $rows = $myOpenUser->getPage("*", $page, $page_size, $condition ." ORDER BY ". str_replace("-", " ", $order));

    $random = time();

    $loginTypeMap = $myOpenUser->getLoginTypeMap();
    $sexMap = $myOpenUser->getSexMap();

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = array();
        $dataArray["{no}"]         = $rows[$i]['no'];
        $dataArray["{user_no}"]    = $rows[$i]['user_no'];
        $dataArray["{unionid}"]    = $rows[$i]['unionid'];
        $dataArray["{openid}"]     = $rows[$i]['openid'];
        $dataArray["{nickname}"]   = $rows[$i]['nickname'];
        $dataArray["{language}"]   = $rows[$i]['language'];
        $dataArray["{city}"]       = $rows[$i]['city'];
        $dataArray["{province}"]   = $rows[$i]['province'];
        $dataArray["{country}"]    = $rows[$i]['country'];
        $dataArray["{headimgurl}"] = empty($rows[$i]['headimgurl']) ? '/images/default_user.png' : $rows[$i]['headimgurl'];
        $dataArray["{add_time}"]   = $rows[$i]['add_time'];
        $dataArray["{sex}"]        = $sexMap[ $rows[$i]['sex'] ];
        $dataArray["{login_type}"] = $loginTypeMap[ $rows[$i]['login_type'] ];

        $dataArray["{user_name}"] = "";
        if( !empty($rows[$i]['user_no']) )
        {
            $dataArray["{user_name}"] = $myUser->getValue("nickname", "no=".$rows[$i]['user_no']);
        }

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