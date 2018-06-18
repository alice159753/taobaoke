<?php

    include_once("config.php");

    $myArticle = new Article($myMySQL);

    $page = !empty($_REQUEST["page"]) ? $_REQUEST["page"] : 1;

    $myTemplate = new Template(TEMPLATE_DIR ."/subject_page.ajax.html");

    include_once("common.inc.php");

    $page_size = 10;

    $condition = "is_through = 'Y'";

    $total_page = $myArticle->getPageCount($page_size, $condition);

    $total_page = ($total_page == 0) ? 1 : $total_page;

    if( $page > $total_page )
    {
        Output::succ('', array('html' => ''));
    }

    $rows = $myArticle->getPage("*", $page, $page_size, $condition ." ORDER BY add_time DESC");

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
 
    $myTemplate->process();
    $html = $myTemplate->getContent();

    Output::succ('', array('html' => $html));

?>