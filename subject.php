<?php

    include_once("config.php");


    $myTemplate = new Template(TEMPLATE_DIR ."/subject.html");

    include_once("common.inc.php");

    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $myTemplate->setReplace("other_data", $dataArray);

    $myArticle = new Article($myMySQL);

    $page_size = 10;
    $condition = "is_through = 'Y'";
    
    $total_page = $myArticle->getPageCount($page_size, $condition);

    $rows = $myArticle->getPage("*", 1, $page_size, $condition ." ORDER BY top DESC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myArticle->getData($rows[$i]);

        $url = $dataArray['{url}'];

        $myTemplate->setReplace("subject", $dataArray);

        $picLists = explode("##", $rows[$i]['pic_lists']);

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