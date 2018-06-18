<?php

    include_once("config.php");

    $article_no = !empty($_REQUEST["article_no"]) ? $_REQUEST["article_no"] : "0";
    $user_no = isset($_SESSION['user_no']) ? $_SESSION['user_no'] : 0;
    $collect = isset($_REQUEST['collect']) ? $_REQUEST['collect'] : '';

    if( empty($article_no) || empty($collect) )
    {
        header('Location:index.php');
        exit;
    }

    if( empty($user_no) )
    {
        //JavaScript::alertAndRedirect("请登录！", "user_login.php?r=".time());
        echo ' 请登录！';

        exit;
    }
    
    $myArticle = new Article($myMySQL);
    $myUserCollect = new UserCollect($myMySQL);

    //收藏
    if( $collect == 'collect' )
    {
        $userCollectRow = $myUserCollect->getRow("*","user_no = $user_no AND article_no = $article_no");

        if( !empty($userCollectRow) )
        {
            echo 'ok';
            exit;
        }

        $dataArray = array();
        $dataArray['user_no'] = $user_no;
        $dataArray['article_no'] = $article_no;
        $dataArray['add_time'] = 'now()';

        $myUserCollect->addRow($dataArray);
    }
    else
    {
       $myUserCollect->delete("user_no = $user_no AND article_no = $article_no");
    }

    echo 'ok';
?>