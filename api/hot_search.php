<?php

    include_once("../config.cmd.php");

    $myHotSearch = new HotSearch($myMySQL);

    $rows = $myHotSearch->getRows("*", "1=1");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myHotSearch->getDataClean($rows[$i]);

        $result[] = $dataArray;
    }

    Output::succ('', $result);
    


?>