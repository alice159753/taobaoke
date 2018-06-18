<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Admin.php");
    ob_clean();

    $no_list = isset($_REQUEST["no_list"]) ? $_REQUEST["no_list"] : 0;

    if( empty($no_list) )
    {
        echo 'error: no not empty';
        exit;
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myAdmin = new Admin($myMySQL);

    $noList = explode(",", $no_list);

    for($i = 0; isset($noList[$i]); $i++)
    {
        if( empty($noList[$i]) )
        {
            continue;
        }

        $myAdmin->remove("no = ". $noList[$i]);
    }

    echo 'ok';

?>