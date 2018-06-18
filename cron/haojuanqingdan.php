<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");

    ob_start();
    include_once(INCLUDE_DIR. "/TaobaoApi.php");
    ob_clean();

    $myProduct = new Product($myMySQL);

    $myTaobaoApi = new TaobaoApi();

    for($i = 1; $i <= 200; $i++)
    {
        $lists = $myTaobaoApi->getPointList('', $i, 100);

        echo "count=".count($lists)."\n";

        foreach ($lists as $key => $item) 
        {
            $item = (array)$item;

            if( empty($item) )
            {
                echo "item empty\n";
                continue;
            }

            haojuanToproduct($myProduct, $item);
        }
    }


     //好卷清单的商品转换为product
    function haojuanToproduct($myProduct, $item = array())
    {
        $myTaobaoApi = new TaobaoApi();

        $dataArray = array();
        $dataArray['price_ratio']       = $item['commission_rate'];
        $dataArray['coupon_link']       = $item['coupon_click_url'];
        $dataArray['coupon_end_date']   = $item['coupon_end_time'];
        $dataArray['coupon_title']      = $item['coupon_info'];
        $dataArray['coupon_residue']    = $item['coupon_remain_count'];
        $dataArray['coupon_start_date'] = $item['coupon_start_time'];
        $dataArray['coupon_num']        = $item['coupon_total_count'];
        $dataArray['title']             = $item['title'];
        $dataArray['shop_name']         = $item['shop_title'];
        $dataArray['product_id']        = $item['num_iid'];
        $dataArray['detail_url']        = $item['item_url'];
        $dataArray['pic_url']           = $item['pict_url'];
        $sale_num                       = str_replace("月销量","", $item['nick']);
        $sale_num                       = str_replace("件","", $sale_num);
        $dataArray['sale_num']          = $item['volume'];
        $dataArray['sekerclean']        = $item['seller_id'];

        $list = explode("减", $item['coupon_info']);
        if( !empty($list[1]) )
        {
           $coupon_save_price = str_replace("元", "", $list[1]);
           $coupon_save_price = trim($coupon_save_price);
           $dataArray['coupon_save_price'] = $coupon_save_price;
        }
        else
        {
            $coupon_save_price = str_replace("元无条件券", "", $item['coupon_info']);
            $coupon_save_price = trim($coupon_save_price);
            $dataArray['coupon_save_price'] = $coupon_save_price;
        }

        $dataArray['price'] = $item['zk_final_price'] * 100;
        $dataArray['fee'] = $item['commission_rate'] * ($item['zk_final_price']-$dataArray['coupon_save_price']);

        //获取商品详情
        $productInfo = $myTaobaoApi->getProductInfo($dataArray['product_id']);
        $productInfo['shop_info'] = $myTaobaoApi->getFavoritesInfo($dataArray['shop_name']);
        $product_info_json = json_encode($productInfo, JSON_UNESCAPED_UNICODE);

        $dataArray['product_info'] = $product_info_json;

        $dataArray['category_no'] = 23;
        $dataArray['category_no1'] = 23; //一级分类
        $dataArray['is_online'] = 'Y';

        //创建淘口令
        $dataArray['coupon_command'] = $myTaobaoApi->makeTaokouling($dataArray['title'], $dataArray['coupon_link'], $dataArray['pic_url']);

        $row = $myProduct->getRow("*", "product_id = '".$dataArray['product_id']."'");

        if( empty($row) )
        {
            $dataArray['add_time'] = 'now()';
            $myProduct->addRow($dataArray);
        }
        else
        {
            //更新
            $dataArray['update_time'] = 'now()';
            $myProduct->update($dataArray, "product_id = '".$dataArray['product_id']."'");
        }

        return $dataArray;
    }

?>