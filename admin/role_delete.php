<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    $no = isset($_REQUEST["no"]) ? $_REQUEST["no"] : 0;

    if ( $_REQUEST["no"] == 0 )
    {
        JavaScript::alertAndRedirect("error", "role.php?r=".time());
        exit;
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myRole = new Role($myMySQL);

    $myRole->remove("no = ". $no);

    //JavaScript::alertAndRedirect("删除成功！", "role.php?".http_build_query($_REQUEST)."&r=".time());
    JavaScript::alertAndBack("删除成功！!",'-1');

?>