<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");

    //如果coupon_save_price为空，则下架
    //优惠卷开始和结束日期为空则下架

    $myProduct = new Product($myMySQL);

    $rows = $myProduct->getRows("*", "1=1");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $is_online = 'N';
        if( empty($rows[$i]['coupon_save_price']) )
        {
            $is_online = 'N';
        }

        //没有优惠卷
        if( empty($rows[$i]['coupon_start_date']) || empty($rows[$i]['coupon_end_date']) )
        {
            $is_online = 'Y';
        }

        $today = strtotime(date('Y-m-d', strtotime('now')));

        if( $today >= strtotime($rows[$i]['coupon_start_date']) && $today <= strtotime($rows[$i]['coupon_end_date']) )
        {
            $is_online = 'Y';
        }

        if( $is_online == 'N' && $rows[$i]['category_no'] != 20 && $rows[$i]['category_no'] != 21 )
        {

            $myProduct->remove("no = ". $rows[$i]['no']);

            continue;
        }

        $dataArray = array();
        $dataArray['is_online'] = $is_online;

        $myProduct->update($dataArray, "no = ". $rows[$i]['no']);
    }

    echo 'ok';


?>