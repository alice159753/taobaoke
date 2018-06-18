<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Suggest.php");
    ob_clean();

    // request
    $no    = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;
    $reply_content = !empty($_REQUEST["reply_content"]) ? trim($_REQUEST["reply_content"]) : "" ;

    if ( $no == 0 )
    {
        header("Location: suggest.php?r=".time());
        exit;
    }
   
    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySuggest= new Suggest($myMySQL);

    $row = $mySuggest->getRow("no = $no");

    if( !empty($row['reply_content']) )
    {
        Output::error('已经回复该反馈',array(), 1);
    }
    
    // $dataArray = array();
    // $dataArray['user_no']  = $row['user_no'];
    // $dataArray['content']  = $reply_content;
    // $dataArray['type']     = 1;
    // $dataArray['is_read']  = 'N';
    // $dataArray['add_time'] = 'now()';

    // $myUserMessage->addRow($dataArray);
    // $user_message_no = $myUserMessage->getInsertID();

    $user_message_no = 0;

    $dataArray = array();
    $dataArray['update_time']     = 'now()';
    $dataArray['reply_content']   = $reply_content;
    $dataArray['user_message_no'] = $user_message_no;
    
    $mySuggest->update($dataArray, "no = ". $no);

    Output::succ('修改成功',array());

?>