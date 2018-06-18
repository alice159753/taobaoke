<?php

    include_once("config.php");

    include_once("session_check.php");

    $myProduct = new Product($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);
    $myArticle = new Article($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/user_collect_subject.html");

    include_once("common.inc.php");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    $page_size = 5;

    $condition = "user_no = ".$_SESSION['user_no']." AND article_no != 0 ";
  
    $total_page = $myUserCollect->getPageCount($page_size, $condition);

    $rows = $myUserCollect->getPage("*", 1, $page_size, $condition ." ORDER BY add_time DESC");

    if( empty($rows) )
    {
        $myTemplate->setReplace("empty", array());
    }

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

    $dataArray = array();
    $dataArray["{total_page}"] = $total_page;

    $myTemplate->setReplace("page", $dataArray);

    $myTemplate->process();
    $myTemplate->output();


?>