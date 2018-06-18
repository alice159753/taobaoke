<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Article.php");
    include_once(INCLUDE_DIR. "/FileTools.php");
    include_once(INCLUDE_DIR ."/Image.php");
    ob_clean();

    $no = !empty($_REQUEST["no"]) ? trim($_REQUEST["no"]) : 0;

    if( empty($no) )
    {
        header('Location:article.php');
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myArticle = new Article($myMySQL);

    $row = $myArticle->getRow("*", "1=1 ORDER BY top DESC");

    $dataArray = array();
    $dataArray['top'] = $row['top'] + 1;
    $dataArray['update_time'] = 'now()';

    $myArticle->update($dataArray, "no = $no");

    Output::succ('设置成功',array());

?>