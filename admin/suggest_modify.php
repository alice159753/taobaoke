<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Suggest.php");
    include_once(INCLUDE_DIR. "/User.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: suggest.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/suggest_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySuggest = new Suggest($myMySQL);
    $myUser = new User($myMySQL);

    $row = $mySuggest->get("*", "no = $no");
    
    $dataArray = array();
    $dataArray["{no}"]              = $row['no'];
    $dataArray["{user_no}"]         = $row['user_no'];
    $one                            = $myUser->getRow("*", "no =".$row['user_no']);
    $dataArray["{nickname}"]        = $one['nickname'];
    $dataArray["{content}"]         = $row['content'];
    $dataArray["{reply_content}"]   = $row['reply_content'];
    $dataArray["{add_time}"]        = $row['add_time'];
    $dataArray["{update_time}"]     = $row['update_time'];
    $dataArray["{user_message_no}"] = $row['user_message_no'];

    $myTemplate->setReplace("data", $dataArray);

    $myTemplate->process();
    $myTemplate->output();
?>