<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Category.php");
    include_once(INCLUDE_DIR. "/TaobaoApi.php");
    ob_clean();
    

	class Product extends Table
	{
	    function Product($myMySQL, $table = "product")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getIsOnline()
        {
            return array('Y' => '是', 'N' => '否');
        }

        function getData($row)
        {
            $isOnlineMap = $this->getIsOnline();

            $myCategory = new Category($this->myMySQL);

            $dataArray = array();
            $dataArray['{no}']                   = $row['no'];
            $dataArray['{product_id}']           = $row['product_id'];
            $dataArray['{title}']                = $row['title'];
            $dataArray['{pic_url}']              = $row['pic_url'];
            $dataArray['{detail_url}']           = $row['detail_url'];
            $dataArray['{shop_name}']            = $row['shop_name'];
            $dataArray['{price}']                = $row['price'] /100;
            $dataArray['{sale_num}']             = $row['sale_num'];
            $dataArray['{price_ratio}']          = $row['price_ratio'];
            $dataArray['{fee}']                  = $row['fee'] /100;
            $dataArray['{sekerclean}']           = $row['sekerclean'];
            $dataArray['{taobao_short_link}']    = $row['taobao_short_link'];
            $dataArray['{taobao_link}']          = $row['taobao_link'];
            $dataArray['{taobao_command}']       = $row['taobao_command'];
            $dataArray['{coupon_num}']           = $row['coupon_num'];
            $dataArray['{coupon_residue}']       = $row['coupon_residue'];
            $dataArray['{coupon_title}']         = $row['coupon_title'];
            $dataArray['{coupon_save_price}']    = $row['coupon_save_price'];
            $dataArray['{coupon_start_date}']    = $row['coupon_start_date'];
            $dataArray['{coupon_end_date}']      = $row['coupon_end_date'];
            $dataArray['{coupon_short_link}']    = $row['coupon_short_link'];
            $dataArray['{coupon_link}']          = empty($row['coupon_link']) ? $row['taobao_link']: $row['coupon_link'];
            $dataArray['{coupon_button_title}']  = empty($row['coupon_link']) ? '立即购买' : '立即抢卷';
            $dataArray['{coupon_command}']       = $row['coupon_command'];
            $dataArray['{is_marketing}']         = $row['is_marketing'];
            $dataArray['{add_time}']             = $row['add_time'];
            $dataArray['{update_time}']          = $row['update_time'];
            $dataArray['{label}']                = $row['label'];
            $dataArray['{activity_state}']       = $row['activity_state'];
            $dataArray['{activity_price_ratio}'] = $row['activity_price_ratio'];
            $dataArray['{activity_fee}']         = $row['activity_fee'];
            $dataArray['{activity_start_date}']  = $row['activity_start_date'];
            $dataArray['{activity_end_date}']    = $row['activity_end_date'];

            $dataArray['{sale_price}']       = $row['price'] /100 - $row['coupon_save_price'];

            $dataArray['{is_online}']       = $row['is_online'];
            $dataArray['{is_online_title}'] = $isOnlineMap[ $row['is_online'] ];

            $dataArray['{category_no}']    = $row['category_no'];
            $dataArray['{product_info}']   = print_r(json_decode($row['product_info'], true), true);

            $dataArray['{category_title}'] = "";
            if( !empty($row['category_no']) )
            {
                $categoryRow = $myCategory->getRow("*", "no = ". $row['category_no']);

                $dataArray['{category_title}'] = $categoryRow['title'];
            }   

            $dataArray['{coupon_start_date_format}'] = "";
            $dataArray['{coupon_end_date_format}'] = ""; 

            if( $row['coupon_start_date'] != '0000-00-00' && $row['coupon_end_date'] != '0000-00-00' )
            {
                $dataArray['{coupon_start_date_format}'] = date('m.d', strtotime($row['coupon_start_date']));
                $dataArray['{coupon_end_date_format}']   = date('m.d', strtotime($row['coupon_end_date']));
            }

            $product_info = json_decode($row['product_info'], true);

            if( isset($product_info['shop_info']['shop_type']) )
            {
                $dataArray['{shop_pic}'] = $product_info['shop_info']['shop_type'] == 'B' ? 'tmall.png' : 'taobao.png';
            }
            else
            {
                $dataArray['{shop_pic}'] = $product_info['shop_info'][0]['shop_type'] == 'B' ? 'tmall.png' : 'taobao.png';
            }

            $dataArray['{article_url}'] = URL."/product_detail.php?no=".$row['no'];

            $dataArray['{add_time_f}'] = date('Y.m.d', strtotime($row['add_time']));

            return $dataArray;
        }

        function getDataClean($row)
        {
            $isOnlineMap = $this->getIsOnline();

            $myCategory = new Category($this->myMySQL);

            $dataArray = array();
            $dataArray['no']                   = $row['no'];
            $dataArray['product_id']           = $row['product_id'];
            $dataArray['title']                = $row['title'];
            $dataArray['pic_url']              = $row['pic_url'];
            $dataArray['detail_url']           = $row['detail_url'];
            $dataArray['shop_name']            = $row['shop_name'];
            $dataArray['price']                = $row['price'] /100;
            $dataArray['sale_num']             = $row['sale_num'];
            $dataArray['price_ratio']          = $row['price_ratio'];
            $dataArray['fee']                  = $row['fee'] /100;
            $dataArray['sekerclean']           = $row['sekerclean'];
            $dataArray['taobao_short_link']    = $row['taobao_short_link'];
            $dataArray['taobao_link']          = $row['taobao_link'];
            $dataArray['taobao_command']       = $row['taobao_command'];
            $dataArray['coupon_num']           = $row['coupon_num'];
            $dataArray['coupon_residue']       = $row['coupon_residue'];
            $dataArray['coupon_title']         = $row['coupon_title'];
            $dataArray['coupon_save_price']    = $row['coupon_save_price'];
            $dataArray['coupon_start_date']    = $row['coupon_start_date'];
            $dataArray['coupon_end_date']      = $row['coupon_end_date'];
            $dataArray['coupon_short_link']    = $row['coupon_short_link'];
            $dataArray['coupon_link']          = empty($row['coupon_link']) ? $row['taobao_link']: $row['coupon_link'];
            $dataArray['coupon_button_title']  = empty($row['coupon_link']) ? '立即购买' : '立即抢卷';
            $dataArray['coupon_command']       = $row['coupon_command'];
            $dataArray['is_marketing']         = $row['is_marketing'];
            $dataArray['add_time']             = $row['add_time'];
            $dataArray['update_time']          = $row['update_time'];
            $dataArray['label']                = $row['label'];
            $dataArray['activity_state']       = $row['activity_state'];
            $dataArray['activity_price_ratio'] = $row['activity_price_ratio'];
            $dataArray['activity_fee']         = $row['activity_fee'];
            $dataArray['activity_start_date']  = $row['activity_start_date'];
            $dataArray['activity_end_date']    = $row['activity_end_date'];

            $dataArray['sale_price']       = $row['price'] /100 - $row['coupon_save_price'];

            $dataArray['is_online']       = $row['is_online'];
            $dataArray['is_online_title'] = $isOnlineMap[ $row['is_online'] ];

            $dataArray['category_no']    = $row['category_no'];
            $dataArray['product_info']   = print_r(json_decode($row['product_info'], true), true);

            $dataArray['category_title'] = "";
            if( !empty($row['category_no']) )
            {
                $categoryRow = $myCategory->getRow("*", "no = ". $row['category_no']);

                $dataArray['category_title'] = $categoryRow['title'];
            }   

            $dataArray['coupon_start_date_format'] = "";
            $dataArray['coupon_end_date_format'] = ""; 

            if( $row['coupon_start_date'] != '0000-00-00' && $row['coupon_end_date'] != '0000-00-00' )
            {
                $dataArray['coupon_start_date_format'] = date('m.d', strtotime($row['coupon_start_date']));
                $dataArray['coupon_end_date_format']   = date('m.d', strtotime($row['coupon_end_date']));
            }

            $product_info = json_decode($row['product_info'], true);

            if( isset($product_info['shop_info']['shop_type']) )
            {
                $dataArray['shop_pic'] = $product_info['shop_info']['shop_type'] == 'B' ? 'tmall.png' : 'taobao.png';
            }
            else
            {
                $dataArray['shop_pic'] = $product_info['shop_info'][0]['shop_type'] == 'B' ? 'tmall.png' : 'taobao.png';
            }

            $dataArray['article_url'] = URL."/product_detail.php?no=".$row['no'];

            $dataArray['add_time_f'] = date('Y.m.d', strtotime($row['add_time']));

            return $dataArray;
        }

	}

   

?>