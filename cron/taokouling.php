<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");

    ob_start();
    include_once(INCLUDE_DIR. "/TaobaoApi.php");
    ob_clean();

    //淘口令30天后会失效，所以要定时更新淘口令
    $myProduct = new Product($myMySQL);
    $myTaobaoApi = new TaobaoApi();

    $date = date('Y-m-d 00:00:00', strtotime('-29 day'));

    $rows = $myProduct->getRows("*", "update_time < '$date'");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $dataArray = array();
        $dataArray['update_time'] = date('Y-m-d H:i:s');

        $link = empty($rows[$i]['taobao_link']) ? $rows[$i]['coupon_link'] : $rows[$i]['taobao_link'];

        $coupon_command = $myTaobaoApi->makeTaokouling($rows[$i]['title'], $link, $rows[$i]['pic_url']);

        if( empty($coupon_command) )
        {
            continue;
        }

        $dataArray['coupon_command'] = $coupon_command;

        $myProduct->update($dataArray, "no = ". $rows[$i]['no']);
    }

    echo 'ok';


?>