<?php

    include_once("../config.cmd.php");

    $myCategory = new Category($myMySQL);

    $parent_no = isset($_REQUEST["parent_no"]) ? $_REQUEST["parent_no"] : "0";

    //轮播图, 最多展示5条
    $rows = $myCategory->getRows("*", "is_show = 'Y' AND parent_no = 0 ORDER BY sort ASC");

    $result = array();
    $result['category1list'] = $rows;

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataRows = $myCategory->getRows("*", "is_show = 'Y' AND parent_no = ".$rows[$i]['no']." ORDER BY sort ASC");

        foreach ($dataRows as $index => $item) 
        {
            $dataArray = $myCategory->getDataClean($item);

            $result['category2list'][ $rows[$i]['no'] ][] = $dataArray;
        }

        if( empty($dataRows) )
        {
            $dataArray = $myCategory->getDataClean($rows[$i]);

            //如果二级分类为空，则
            $result['category2list'][ $rows[$i]['no'] ][] = $dataArray;
        }

    }

    Output::succ('', $result);
    


?>