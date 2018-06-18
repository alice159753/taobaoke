<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");

    //删除价格过高的商品
    $myProduct = new Product($myMySQL);

    $rows = $myProduct->getRows("*", "1=1");

    for($i = 0; isset($rows[$i]); $i++)
    {
        //如果价格太贵则下线
        if( $rows[$i]['price'] >= 100000 )
        {
            $dataArray = array();
            $dataArray['is_online'] = 'N';

            $myProduct->update($dataArray, "no = ". $rows[$i]['no']);
        }

        //如果存在优惠卷日期则，则判断是否过期
        if( isset($rows[$i]['coupon_end_date']) && !empty($rows[$i]['coupon_end_date']) && $rows[$i]['coupon_end_date'] != "0000-00-00" )
        {
            $today = strtotime(date('Y-m-d', strtotime('now')));

            if( $today > strtotime($rows[$i]['coupon_end_date']) )
            {
                $dataArray = array();
                $dataArray['is_online'] = 'N';
echo "outdate no=".$rows[$i]['no']."\n";
                $myProduct->update($dataArray, "no = ". $rows[$i]['no']);
            }
        }

        //双12
        if( $rows[$i]['category_no'] == 22 )
        {
            $dataArray = array();
            $dataArray['is_online'] = 'N';

            $myProduct->update($dataArray, "no = ". $rows[$i]['no']);
        }

    }

    echo 'ok';


?>