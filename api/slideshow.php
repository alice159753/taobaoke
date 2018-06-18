<?php

    include_once("../config.cmd.php");

    $mySlideshow = new Slideshow($myMySQL);

    //轮播图, 最多展示5条
    $rows = $mySlideshow->getRows("*", "1=1 LIMIT 5");

    $result = array();
    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = $mySlideshow->getDataClean($rows[$i]);

        $result[] = $dataArray;
    }

    Output::succ('', $result);
    


?>