<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");

    //如果coupon_save_price为空，则下架
    //优惠卷开始和结束日期为空则下架

    $myProduct = new Product($myMySQL);
    $myCurl = new Curl();

    $rows = $myProduct->getRows("*", "1=1");

    for($i = 0; isset($rows[$i]); $i++)
    {
        $product_info = $rows[$i]['product_info'];
        $product_info = json_decode($product_info, true);

        if( !empty($product_info['description']) )
        {
            continue;
        }

        $html = $myCurl->getContent($product_info['item_url']);

        //天猫
        list($temp, $content) = explode('<div id="description" class="J_DetailSection tshop-psm tshop-psm-bdetaildes">', $html);
        list($content, $temp) = explode('<div id="J_DcBottomRightWrap">', $content);

        $http = strstr($product_info['item_url'], 'https') ? "http" : "https";

        $imageList = Tools::makeImageLists($html, $http);

        $product_info['description'] = $imageList;
        $product_info_json = json_encode($product_info, JSON_UNESCAPED_UNICODE);
     
        //淘宝
        if( empty($content) )
        {
            list($temp, $content) = explode('descUrl          : location.protocol===\'http:\' ?', $html);
            list($content, $temp) = explode(',', $content);

            list($http_url, $https_url) = explode(':', $content);

            $url = strstr($product_info['item_url'], 'https') ? "http:".trim($http_url) : "https:".trim($https_url);
            $url = str_replace("'", "", $url);

            $html = $myCurl->getContent($url);

            $html = str_replace("var desc='", "", $html);
            $html = str_replace("';", "", $html);

            $imageList = Tools::makeImageLists($html, $http);

            $product_info['description'] = $imageList;
            $product_info_json = json_encode($product_info, JSON_UNESCAPED_UNICODE);
        }

        $dataArray = array();
        $dataArray['product_info'] = $product_info_json;

        $myProduct->update($dataArray, "no = ". $rows[$i]['no']);
    }

    echo 'ok';


?>