<?php

    include_once("config.php");

    $no = !empty($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    $myTemplate = new Template(TEMPLATE_DIR ."/subject_detail.html");

    include_once("common.inc.php");

    $myArticle = new Article($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);

    $condition = "no = $no AND is_through = 'Y'";
    
    $row = $myArticle->getRow("*", $condition);

    if( empty($row) )
    {
        JavaScript::alertAndRedirect("该头条已经下线", "subject.php?r=".time());

        exit;
    }

    //更新点击次数
    $dataArray = array();
    $dataArray['view_count'] = $row['view_count'] + 1;

    $myArticle->update($dataArray, "no = $no");
    
    //title
    $dataArray = array();
    $dataArray["{site_title}"] = $systemConfigRow['site_title'];
    $dataArray["{title}"] = $row['title'];

    $myTemplate->setReplace("other_data", $dataArray);


    $dataArray = $myArticle->getData($row);
 
    $row['content'] = preg_replace( '/(<img.*?)((width)=[\'"]+[0-9]+[\'"]+)/', '$1' , $row['content']); 
    $dataArray['{content}'] = preg_replace( '/(<img.*?)((height)=[\'"]+[0-9]+[\'"]+)/', '$1' , $row['content']); 

    $url = $dataArray['{url}'];
 
    //判断是否收藏
    $dataArray['{css_style}'] = '';
    if( !empty($_SESSION['user_no']) )
    {
        $userCollectRow = $myUserCollect->getRow("*", "user_no = ". $_SESSION['user_no']." AND article_no = $no");
        $dataArray['{css_style}'] = empty($userCollectRow) ? '' : 'active';
    }

    $myTemplate->setReplace("subject", $dataArray);
 
    $picLists = explode("##", $rows[$i]['pic_lists']);
 
    for($j = 0; isset($picLists[$j]); $j++)
    {
        $dataArray = array();
        $dataArray['{pic_url}'] = $picLists[$j];
        $dataArray['{url}'] = $url;
 
        $myTemplate->setReplace("pic", $dataArray, 2);
    }

    $myTemplate->process();
    $myTemplate->output();



?>