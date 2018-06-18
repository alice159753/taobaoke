<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/PayOrder.php");
    include_once(INCLUDE_DIR. "/FileTools.php");
    include_once(INCLUDE_DIR ."/Image.php");
    include_once(INCLUDE_DIR ."/ImageCrop.php");
    include_once(INCLUDE_DIR ."/UserIntegral.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    $title = !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : "" ;
    $integral = !empty($_REQUEST["integral"]) ? $_REQUEST["integral"] : '0';

    if ( $no == 0 )
    {
        header("Location: pay_order.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myPayOrder = new PayOrder($myMySQL);
    $myUserIntegral = new UserIntegral($myMySQL);

     // check no
    $row = $myPayOrder->get("*", "no = $no");

    if ( !isset($row["no"]) )
    {
        Output::error('无数据',array(), 1);
    }

    $dataArray = array();
    $dataArray['title']         = $title;
    $dataArray['integral']      = $integral;
    $dataArray['update_time']   = 'now()';

    $myPayOrder->update($dataArray, "no = ". $no);

    //添加到用户积分表
    $title_1 = "订单编号：".$row['order_no'].";  ".$title;
    $myUserIntegral->getRow("*", "title = '$title_1'");

    $dataArray = array();
    $dataArray['title']    = $title_1;
    $dataArray['user_no']  = $row['user_no'];
    $dataArray['add_time'] = 'now()';
    $dataArray['integral'] = $integral;

    $myUserIntegral->addRow($dataArray);

    Output::succ('修改成功',array());

?>