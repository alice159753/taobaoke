<?php

    include_once("../config.cmd.php");

    $myCategory = new Category($myMySQL);

    $parent_no = isset($_REQUEST["parent_no"]) ? $_REQUEST["parent_no"] : "0";

    //轮播图, 最多展示5条
    $rows = $myCategory->getRows("*", "is_show = 'Y' AND parent_no = $parent_no ORDER BY top DESC");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myCategory->getDataClean($rows[$i]);

        $result[] = $dataArray;
    }

    Output::succ('', $result);
    


?>