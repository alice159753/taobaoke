<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");

    ob_start();
    include_once(INCLUDE_DIR. "/TaobaoApi.php");
    include_once(INCLUDE_DIR. "/Taoqianggou.php");
    ob_clean();

    //每次抓取最近30天的淘抢购活动
    $myTaoqianggou = new Taoqianggou($myMySQL);
    $myTaobaoApi = new TaobaoApi();


    $start_time = date('Y-m-d H:i:s');
    $end_time = date('Y-m-d H:i:s', strtotime('+30 day'));

    //添加新的
    $rows = $myTaobaoApi->getTaohaigou('161812076', $start_time, $end_time, 1, 400);
    for($i = 0; isset($rows[$i]); $i++)
    {
        $rows[$i] = (array)$rows[$i];

        $dataArray = array();
        $dataArray['category_name']  = $rows[$i]['category_name'];
        $dataArray['click_url']      = $rows[$i]['click_url'];
        $dataArray['end_time']       = $rows[$i]['end_time'];
        $dataArray['num_iid']        = $rows[$i]['num_iid'];
        $dataArray['pic_url']        = $rows[$i]['pic_url'];
        $dataArray['reserve_price']  = $rows[$i]['reserve_price'];
        $dataArray['sold_num']       = $rows[$i]['sold_num'];
        $dataArray['start_time']     = $rows[$i]['start_time'];
        $dataArray['title']          = $rows[$i]['title'];
        $dataArray['total_amount']   = $rows[$i]['total_amount'];
        $dataArray['zk_final_price'] = $rows[$i]['zk_final_price'];

        $row = $myTaoqianggou->getRow("*", "num_iid = ". $rows[$i]['num_iid']);
        if( !empty($row) )
        {
            $myTaoqianggou->update($dataArray, "num_iid = ". $rows[$i]['num_iid']);
        }
        else
        {
            $myTaoqianggou->addRow($dataArray);
        }
    }

    //删除过期的
    $date = date('Y-m-d 00:00:00');
    $rows = $myTaoqianggou->getRows("*", "end_time < '$date'");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $myTaoqianggou->remove("no = ". $rows[$i]['no']);
    }

    echo 'ok'


?>