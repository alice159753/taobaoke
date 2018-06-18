<?php

    include_once("../config.cmd.php");

    $myCategory = new Category($myMySQL);

    //轮播图, 最多展示5条
    $rows = $myCategory->getRows("*", "is_show = 'Y' AND parent_no = 0 ORDER BY top DESC");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $myCategory->getDataClean($rows[$i]);

        $result[] = $dataArray;
    }

    Output::succ('', $result);
    


?>