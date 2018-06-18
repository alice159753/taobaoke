<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Admin.php");
    ob_clean();

    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $_REQUEST["no"] == 0 )
    {
        JavaScript::alertAndRedirect("error", "admin.php?r=".time());
        exit;
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myAdmin = new Admin($myMySQL);

    $myAdmin->remove("no = ". $no);

    JavaScript::alertAndBack("删除成功！!",'-1');

    //JavaScript::alertAndRedirect("删除成功！", "admin.php?".http_build_query($_REQUEST)."&r=".time());

?>