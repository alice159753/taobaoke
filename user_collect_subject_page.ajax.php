<?php

    include_once("config.php");

    include_once("session_check.php");

    $myProduct = new Product($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);
    $myArticle = new Article($myMySQL);

    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;

    $myTemplate = new Template(TEMPLATE_DIR ."/user_collect_subject_page.ajax.html");

    include_once("common.inc.php");

    $page_size = 5;

    $condition = "user_no = ".$_SESSION['user_no']." AND article_no != 0 ";

    $total_page = $myUserCollect->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array('html' => ''));
    }

    $rows = $myUserCollect->getPage("*", $page, $page_size, $condition ." ORDER BY add_time DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $article_no = $rows[$i]['article_no'];

        $productRow = $myArticle->getRow("*", "no = $article_no");
        
        $dataArray = $myArticle->getData($productRow);

        $myTemplate->setReplace("subject", $dataArray);

        $picLists = explode("##", $productRow['pic_lists']);

        for($j = 0; isset($picLists[$j]); $j++)
        {
            $dataArray = array();
            $dataArray['{pic_url}'] = $picLists[$j];
            $dataArray['{url}'] = $url;

            $myTemplate->setReplace("pic", $dataArray, 2);
        }
    }

    $myTemplate->process();
    $html = $myTemplate->getContent();

    Output::succ('', array('html' => $html));

?>