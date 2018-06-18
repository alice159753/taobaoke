<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/PayOrder.php");
    include_once(INCLUDE_DIR. "/User.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: pay_order.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/pay_order_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myPayOrder = new PayOrder($myMySQL);
    $myUser = new User($myMySQL);

    $row = $myPayOrder->get("*", "no = $no");
    
    $dataArray = $myPayOrder->getData($row);

    $one                     = $myUser->getRow("*", "no =".$row['user_no']);
    $dataArray["{nickname}"] = $one['nickname'];

    $myTemplate->setReplace("data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();
?>