<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    // request
    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $no == 0 )
    {
        header("Location: role.php?r=".time());
        exit;
    }
   
    $myTemplate = new Template(TEMPLATE_DIR ."/role_modify.html");
    
    include_once("common.inc.php");

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myRole = new Role($myMySQL);

    $row = $myRole->get("*", "no = $no");
    
    $dataArray = array();
    $dataArray['{no}'] = $row['no'];
    $dataArray['{title}'] = $row['title'];

    $myTemplate->setReplace("data", $dataArray);

    $permissionList = $row['permission'];
    $permissionList = str_replace("#", "", $permissionList);
    $permissionList = explode(",", $permissionList);

    foreach ($permission_config as $key => $permission) 
    {
        $dataArray = array();
        $dataArray['{title}'] = $permission['title'];

        $myTemplate->setReplace("permission", $dataArray, 2);

        foreach ($permission['data'] as $index => $item) 
        {
            $dataArray = array();
            $dataArray['{title}'] = $item['title'];
            $dataArray['{php}'] = $item['php'];
            $dataArray['{checked}'] = in_array($item['php'], $permissionList) ? 'checked' : '';

            $myTemplate->setReplace("list", $dataArray, 3);
        }
    }

    $myTemplate->process();
    $myTemplate->output();
?>