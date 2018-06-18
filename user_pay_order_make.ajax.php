<?php

    include_once("config.php");

    //include_once("session_check.php");

    $order_no = !empty($_REQUEST["order_no"]) ? trim($_REQUEST["order_no"]) : "";

    if( empty($order_no) )
    {
        Output::error('订单编号不能为空！',array(), 1);
    }

    $myPayOrder = new PayOrder($myMySQL);

    $dataArray = array();
    $dataArray['user_no']  = $_SESSION['user_no'];
    $dataArray['order_no'] = $order_no;
    $dataArray['add_time'] = 'now()';

    $myPayOrder->addRow($dataArray);

    Output::succ('提交成功',array());

?>