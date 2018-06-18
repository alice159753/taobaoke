<?php

    include_once("config.php");

    $product_no = !empty($_REQUEST["product_no"]) ? $_REQUEST["product_no"] : "0";
    $user_no = isset($_SESSION['user_no']) ? $_SESSION['user_no'] : 0;
    $collect = isset($_REQUEST['collect']) ? $_REQUEST['collect'] : '';

    if( empty($product_no) || empty($collect) )
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

    $myUserCollect = new UserCollect($myMySQL);

    //收藏
    if( $collect == 'collect' )
    {
        $userCollectRow = $myUserCollect->getRow("*","user_no = $user_no AND product_no = $product_no");

        if( !empty($userCollectRow) )
        {
            echo 'ok';
            exit;
        }

        $dataArray = array();
        $dataArray['user_no'] = $user_no;
        $dataArray['product_no'] = $product_no;
        $dataArray['add_time'] = 'now()';

        $myUserCollect->addRow($dataArray);
    }
    else
    {
       $myUserCollect->delete("user_no = $user_no AND product_no = $product_no");
    }

    echo 'ok';
?>