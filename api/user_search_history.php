<?php

    include_once("../config.cmd.php");

    $mySearch = new Search($myMySQL);

    $user_no = !empty($_REQUEST["user_no"]) ? $_REQUEST["user_no"] : 0;

    if( empty($user_no) )
    {
        Output::succ($msg, array());
    }

    $rows = $mySearch->getRows("*", "user_no = $user_no AND is_display = 1 ORDER BY no DESC LIMIT 20");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $mySearch->getDataClean($rows[$i]);

        $result[] = $dataArray;
    }

    Output::succ('', $result);
    


?>