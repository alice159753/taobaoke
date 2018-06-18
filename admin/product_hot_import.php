<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/PHPExcel/PHPExcel/IOFactory.php");
    include_once(INCLUDE_DIR. "/Product.php");    
    include_once(INCLUDE_DIR. "/TaobaoApi.php");  
    include_once(INCLUDE_DIR. "/Curl.php");    
    ob_clean();

    $category_no = !empty($_REQUEST["category_no"]) ? $_REQUEST["category_no"] : 0;

    if( empty($category_no)  )
    {
        Output::error('分类不能为空！',array(), 1);
    }

    if( $_FILES["file"]["size"] <= 0 )
    {
        Output::error('请上传正确的文件',array(), 1);
    }

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $myProduct = new Product($myMySQL);
    $myTaobaoApi = new TaobaoApi();
    $myCurl = new Curl();

    if ( $_FILES["file"]["size"] > 0 )
    {
        $pathParts = pathinfo($_FILES["file"]["name"]);
        $filename = rand().uniqid().".".$pathParts["extension"];

        move_uploaded_file($_FILES["file"]["tmp_name"], LOGS_DIR ."/". $filename);
    }

    $myPHPExcel = PHPExcel_IOFactory::load(LOGS_DIR ."/". $filename);

    $sheet = $myPHPExcel->getSheet(0); 
 
    $highestRow = $sheet->getHighestRow(); 

    $highestColumm = $sheet->getHighestColumn(); 

    $mapArray = array('A' => 'product_id', 
                      'B' => 'title', 
                      'C' => 'pic_url', 
                      'D' => 'detail_url', 
                      'E' => 'shop_name', 
                      'F' => 'price', 
                      'G' => 'sale_num',
                      'H' => 'price_ratio', 
                      'I' => 'fee', 
                      'J' => 'sekerclean', 
                      'K' => 'taobao_short_link', 
                      'L' => 'taobao_link',
                      'M' => 'taobao_command',
                      'N' => 'coupon_num',
                      'O' => 'coupon_residue', 
                      'P' => 'coupon_title',
                      'Q' => 'coupon_start_date',
                      'R' => 'coupon_end_date',
                      'S' => 'coupon_link',
                      'T' => 'coupon_command',
                      'U' => 'coupon_short_link',
                      'V' => 'is_marketing');


    $mapArray = array('A' => 'product_id', //商品id
                      'B' => 'title', //商品名称
                      'C' => 'pic_url', //商品主图
                      'D' => 'detail_url', //商品详情页链接地址
                      'E' => 'shop_name', //店铺名称
                      'F' => 'price', //商品价格(单位：元)
                      'G' => 'sale_num',//商品月销量
                      'H' => 'price_ratio', // 通用收入比率(%)
                      'I' => 'fee', //通用佣金
                      'J' => 'activity_state', //活动状态
                      'K' => 'activity_price_ratio', //活动收入比率(%)
                      'L' => 'activity_fee', //活动佣金
                      'M' => 'activity_start_date', //活动开始时间
                      'N' => 'activity_end_date', //活动结束时间
                      'O' => 'sekerclean', //卖家旺旺
                      'P' => 'taobao_short_link', //淘宝客短链接(300天内有效)
                      'Q' => 'taobao_link', //淘宝客链接
                      'R' => 'taobao_command', //淘口令(30天内有效)
                      'S' => 'coupon_num', //优惠券总量
                      'T' => 'coupon_residue', //优惠券剩余量
                      'U' => 'coupon_title', //优惠券面额
                      'V' => 'coupon_start_date',//优惠券开始时间
                      'W' => 'coupon_end_date',//优惠券结束时间
                      'X' => 'coupon_link',//优惠券链接
                      'Y' => 'coupon_command',//优惠券淘口令(30天内有效) 
                      'Z' => 'coupon_short_link'//优惠券短链接(300天内有效)
                      );



    $count = 0;
    $errorList = array();
    for ($row = 2; $row <= $highestRow; $row++)
    {
        $dataArray = array();

        $flag_add = true;

        for ($column = 'A'; $column <= $highestColumm; $column++) 
        {
            $value = $sheet->getCell($column.$row)->getValue();

            if( empty($mapArray[$column]) || $mapArray[$column] =='no' )
            {   
                continue;
            }

            if( $mapArray[$column] == 'price' ||  $mapArray[$column] == 'fee' )
            {
               $value = $value * 100;
            }

            //判断商品是否存在
            if( $mapArray[$column] == 'product_id' )
            {
                $product = $myProduct->getRow("*", "product_id = '".$value."'");

                if( !empty($product) )
                {
                    $data = array();
                    $data['product_id'] = $value;
                    $data['index'] = $row;

                    //$errorList[] = $data;

                    $flag_add = false;
                }
            }

            //修改价格
            //满75元减10元，5元无条件券

            if( $mapArray[$column] == 'coupon_title' )
            {
                $list = explode("减", $value);
                if( !empty($list[1]) )
                {
                   $coupon_save_price = str_replace("元", "", $list[1]);

                   $coupon_save_price = trim($coupon_save_price);

                   $dataArray['coupon_save_price'] = $coupon_save_price;
                }
                else
                {
                    $coupon_save_price = str_replace("元无条件券", "", $value);

                    $coupon_save_price = trim($coupon_save_price);

                    $dataArray['coupon_save_price'] = $coupon_save_price;
                }
            }

            $dataArray[ $mapArray[$column] ] = $value;
        }

        $dataArray['add_time'] = 'now()';
        $dataArray['update_time'] = 'now()';
        $dataArray['category_no'] = $category_no;
        $dataArray['is_online'] = 'Y';

        //获取商品详情
        $productInfo = $myTaobaoApi->getProductInfo($dataArray['product_id']);
        $productInfo['shop_info'] = $myTaobaoApi->getFavoritesInfo($dataArray['shop_name']);
        $product_info_json = json_encode($productInfo, JSON_UNESCAPED_UNICODE);

        $dataArray['product_info'] = $product_info_json;

        if( $flag_add )
        {
            $myProduct->addRow($dataArray);
            ++$count;
        }
        else
        {
            //更新
            $dataArray['update_time'] = 'now()';
            $myProduct->update($dataArray, "product_id = '".$dataArray['product_id']."'");

        }
    }

    $text = '';
    if( !empty($errorList) )
    {
        foreach ($errorList as $index => $item) 
        {
            $text .= "商品id：".$item['product_id'];
        }
    }

    if( empty($errorList) )
    {
       Output::succ('导入成功',array());
    }
    else
    {
        Output::error($text."导入失败，有重复的商品id",array(), 1);
    }

?>