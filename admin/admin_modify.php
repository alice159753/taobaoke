<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Admin.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: admin.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/admin_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myAdmin = new Admin($myMySQL);
    $myRole = new Role($myMySQL);

    $row = $myAdmin->get("*", "no = $no");
    
    $dataArray = array();
    $dataArray["{no}"]       = $row['no'];
    $dataArray["{account}"]  = $row['account'];
    $dataArray["{password}"] = $row['password'];
    $dataArray["{role_no}"]  = $row['role_no'];

    $myTemplate->setReplace("data", $dataArray);

    $rows = $myRole->getRows("*", "1=1 ORDER BY no ASC");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = array();
        $dataArray['{no}']    = $rows[$i]['no'];
        $dataArray['{title}'] = $rows[$i]['title'];
        $dataArray['{selected}'] = $row['role_no'] == $rows[$i]['no'] ? 'selected' : '';

        $myTemplate->setReplace('role', $dataArray, 2);
    }


    $myTemplate->process();
    $myTemplate->output();
?>