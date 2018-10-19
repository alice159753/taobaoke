<?php

include "taobao-sdk/TopSdk.php";


class TaobaoApi
{

    //接口文档地址：http://open.taobao.com/docs/api_list.htm?spm=a219a.7629065.0.0.mYB6lm&cid=38&qq-pf-to=pcqq.c2c
    function TaobaoApi()
    {
        $this->base_url_http = "http://gw.api.taobao.com/router/rest";

        $this->base_url_https = "https://eco.taobao.com/router/rest";

        $this->base_url = $this->base_url_http;

        $this->appkey = '24643106';
        $this->secretKey = '28b59c0ce2864e314122119b66696c81';


    }

    //淘宝客商品查询
    function getProductInfo($num_ids)
    {
        $c = new TopClient();
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;

        $req = new TbkItemInfoGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
        $req->setPlatform("1");
        $req->setNumIids($num_ids);
        $resp = $c->execute($req);
        
        $resp = (array)$resp;

        $resp = (array)$resp['results'];

        $resp = (array)$resp['n_tbk_item'];

        $resp['small_images'] = (array)$resp['small_images'];

        return $resp;

        //SimpleXMLElement Object
        //(
        //    [results] => SimpleXMLElement Object
        //        (
        //            [n_tbk_item] => SimpleXMLElement Object
        //                (
        //                    [item_url] => http://item.taobao.com/item.htm?id=546025600266
        //                    [num_iid] => 546025600266
        //                    [pict_url] => http://img4.tbcdn.cn/tfscom/i1/675822543/TB1aAfVXw6DK1JjSZPhXXa8uVXa_!!0-item_pic.jpg
        //                    [provcity] => 浙江 嘉兴
        //                    [reserve_price] => 125.00
        //                    [small_images] => SimpleXMLElement Object
        //                        (
        //                            [string] => Array
        //                                (
        //                                    [0] => http://img3.tbcdn.cn/tfscom/i4/675822543/TB20wlPkMFkpuFjSspnXXb4qFXa_!!675822543.jpg
        //                                    [1] => http://img1.tbcdn.cn/tfscom/i3/675822543/TB2iEtxkMxlpuFjSszgXXcJdpXa_!!675822543.jpg
        //                                    [2] => http://img4.tbcdn.cn/tfscom/i4/675822543/TB2ZdJMkHXlpuFjy1zbXXb_qpXa_!!675822543.jpg
        //                                    [3] => http://img4.tbcdn.cn/tfscom/i2/675822543/TB29XRpmJBopuFjSZPcXXc9EpXa_!!675822543.jpg
        //                                )

        //                        )

        //                    [title] => 卫衣女连帽套头短款女装春秋季2017新款韩版宽松百搭学生上衣秋装
        //                    [user_type] => 1
        //                    [zk_final_price] => 75.00
        //                )

        //        )

        //    [request_id] => 13oz9hj6o4ewm
        //)


    }

    //淘宝客商品查询
    function getProductInfoByTitle($title)
    {
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;

        $req = new TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ($title);
        $req->setSort("total_sales");
        $req->setIsOverseas("false");
        $req->setPlatform("1");
        $req->setPageNo("1");
        $req->setPageSize("20");
        $resp = $c->execute($req);

        $resp = (array)$resp;

        $resp = (array)$resp['results'];

        $resp = (array)$resp['n_tbk_item'];

        return $resp;

       //[results] => SimpleXMLElement Object
       //(
       //    [n_tbk_item] => Array
       //        (
       //            [0] => SimpleXMLElement Object
       //                (
       //                    [item_url] => http://item.taobao.com/item.htm?id=543482838893
       //                    [nick] => 圣蝶娜旗舰店
       //                    [num_iid] => 543482838893
       //                    [pict_url] => https://img.alicdn.com/tfscom/i4/TB1F_FQOVXXXXbrXpXXXXXXXXXX_!!0-item_pic.jpg
       //                    [provcity] => 江苏 苏州
       //                    [reserve_price] => 168.00
       //                    [seller_id] => 3067447301
       //                    [small_images] => SimpleXMLElement Object
       //                        (
       //                            [string] => Array
       //                                (
       //                                    [0] => https://img.alicdn.com/tfscom/i3/3067447301/TB2YmwfaSXlpuFjy0FeXXcJbFXa_!!3067447301.jpg
       //                                    [1] => https://img.alicdn.com/tfscom/i1/3067447301/TB2ow.oaMJlpuFjSspjXXcT.pXa_!!3067447301.jpg
       //                                    [2] => https://img.alicdn.com/tfscom/i4/3067447301/TB24S.haM0kpuFjSspdXXX4YXXa_!!3067447301.jpg
       //                                    [3] => https://img.alicdn.com/tfscom/i3/3067447301/TB2pjgnaS0jpuFjy0FlXXc0bpXa_!!3067447301.jpg
       //                                )

       //                        )

       //                    [title] => 秋冬新款女装韩版百搭高领套头毛衣女流苏宽松加厚长袖针织打底衫
       //                    [user_type] => 1
       //                    [volume] => 280
       //                    [zk_final_price] => 75.00
       //                )
    }

    //淘宝客店铺查询
    function getFavoritesInfo($q)
    {
        $c = new TopClient();
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;

        $req = new TbkShopGetRequest();
        $req->setFields("user_id,shop_title,shop_type,seller_nick,pict_url,shop_url");
        $req->setQ($q);
        $resp = $c->execute($req);

        $resp = (array)$resp;

        $resp = (array)$resp['results'];

        $resp = (array)$resp['n_tbk_shop'];

        //[pict_url] => http://logo.taobaocdn.com/shop-logo/2b/c5/TB1UXvDNFXXXXawXVXXwu0bFXXX.png
        //[seller_nick] => 克克chloe
        //[shop_title] => AMUZ STUDIO
        //[shop_type] => C
        //[shop_url] => http://store.taobao.com/shop/view_shop.htm?user_number_id=2461293558
        //[user_id] => 2461293558

        //shop_type  B：天猫，C：淘宝

        return $resp;
    }

    //获取淘宝联盟选品库的宝贝信息
    function getProduct()
    {
        $c = new TopClient();
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;

        $req = new TbkUatmFavoritesItemGetRequest();
        $req->setPlatform("1");
        $req->setPageSize("20");
        $req->setPageNo("1");
        $req->setAdzoneId("138292252");
        //$req->setUnid();
        $req->setFavoritesId("12426523");
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");
        $resp = $c->execute($req);

        return $resp;
    }

    //获取淘宝联盟选品库列表
    function getFavoritesLists()
    {   
        $c = new TopClient();
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;

        $req = new TbkUatmFavoritesGetRequest();
        $req->setPageNo("1");
        $req->setPageSize("20");
        $req->setFields("favorites_title,favorites_id,type");
        //$req->setType("1");
        $resp = $c->execute($req);

        $resp = (array)$resp;

        $resp = (array)$resp['results'];

        return $resp;
    }


    //好券清单API【导购】
    function getPointList($title, $page, $pagesize = 100)
    {
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TbkDgItemCouponGetRequest;
        $req->setAdzoneId("172640977");
        $req->setPlatform("1");
        $req->setPageSize("$pagesize");
        $req->setPageNo($page);

        if( !empty($title) )
        {
            $req->setQ($title);
        }

        $resp = $c->execute($req);

        $resp = (array)$resp;

        $resp = (array)$resp['results'];

        $resp = (array)$resp['tbk_coupon'];

        return $resp;



        //[0] => SimpleXMLElement Object
        //(
        //    [category] => 21
        //    [commission_rate] => 15.50
        //    [coupon_click_url] => https://uland.taobao.com/coupon/edetail?e=i22w%2FqKLMigGQASttHIRqV4fCEOg0Q10E5xasqivppQYXB958FVfbAaOGARh%2BcXuA3Q9qS7nkanAYpVwazoiPt3hGvNo6odTm4VLH9mslwz28G6HAXsNx0Rn%2FvX8PCGNb0E3x7EUvXBqIyMYLQEg6PvQFWeqWAJif5%2B7SUtbkLzaUdFGdLbQtQ%3D%3D&traceId=0ab013a015140101359602508e
        //    [coupon_end_time] => 2018-02-18
        //    [coupon_info] => 满21元减20元
        //    [coupon_remain_count] => 36674
        //    [coupon_start_time] => 2017-12-18
        //    [coupon_total_count] => 99999
        //    [item_description] => SimpleXMLElement Object
        //        (
        //        )

        //    [item_url] => http://detail.tmall.com/item.htm?id=560084650460
        //    [nick] => 月销量1866件
        //    [num_iid] => 560084650460
        //    [pict_url] => http://img.alicdn.com/tfscom/i4/3319368315/TB26ri9XysF4uJjSZFtXXXHwVXa_!!3319368315.jpg
        //    [seller_id] => 3319368315
        //    [shop_title] => 熊先生拖鞋
        //    [small_images] => SimpleXMLElement Object
        //        (
        //            [string] => http://img.alicdn.com/tfscom/i3/3319368315/TB2vqU5iaagSKJjy0FgXXcRqFXa_!!3319368315.jpg
        //        )

        //    [title] => 大号暖贴宝宝贴发热贴腰肩颈关节暖宫保暖暖身贴新品100片批包邮
        //    [user_type] => 0
        //    [volume] => 5908
        //    [zk_final_price] => 29.90


    }

    //提供淘客生成淘口令接口，淘客提交口令内容、logo、url等参数，生成淘口令关键key如：￥SADadW￥，后续进行文案包装组装用于传播
    function makeTaokouling($text, $url, $logo)
    {
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TbkTpwdCreateRequest;
        $req->setText($text);
        $req->setUrl($url);
        $req->setLogo($logo);
        $resp = $c->execute($req);

        $resp = (array)$resp;

        $resp = (array)$resp['data'];

        return $resp['model'];
    }

    //淘宝客订单查询, 没有权限
    function getOrder()
    {
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TbkOrderGetRequest;
        $req->setFields("tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id");
        $req->setStartTime("2017-05-23 12:18:22");
        $req->setSpan("600");
        $req->setPageNo("1");
        $req->setPageSize("20");
        $req->setTkStatus("1");
        $req->setOrderQueryType("settle_time");
        $resp = $c->execute($req);
        print_r($resp);

    }

    //淘抢购api
    function getTaohaigou($adzone_id, $start_time, $end_time, $page = 1, $page_size = 40)
    {
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TbkJuTqgGetRequest;
        $req->setAdzoneId($adzone_id); //161812076
        $req->setFields("click_url,pic_url,reserve_price,zk_final_price,total_amount,sold_num,title,category_name,start_time,end_time");
        $req->setStartTime($start_time);
        $req->setEndTime($end_time);
        $req->setPageNo($page);
        $req->setPageSize($page_size);
        $resp = $c->execute($req);

        $resp = (array)$resp;

        $resp = (array)$resp['results'];

        $resp = (array)$resp['results'];

        return $resp;

    }

    function getMaterial()
    {
        $c = new TopClient;
        $c->appkey = $appkey;
        $c->secretKey = $secret;
        $req = new TbkDgMaterialOptionalRequest;
        $req->setStartDsr("10");
        $req->setPageSize("20");
        $req->setPageNo("1");
        $req->setPlatform("1");
        $req->setEndTkRate("1234");
        $req->setStartTkRate("1234");
        $req->setEndPrice("10");
        $req->setStartPrice("10");
        $req->setIsOverseas("false");
        $req->setIsTmall("false");
        $req->setSort("tk_rate_des");
        $req->setItemloc("杭州");
        $req->setCat("16,18");
        $req->setQ("女装");
        $req->setHasCoupon("false");
        $req->setIp("13.2.33.4");
        $req->setAdzoneId("123");
        $req->setNeedFreeShipment("true");
        $req->setNeedPrepay("true");
        $req->setIncludePayRate30("true");
        $req->setIncludeGoodRate("true");
        $req->setIncludeRfdRate("true");
        $req->setNpxLevel("2");
        $resp = $c->execute($req);

        print_r($resp);
    }

}










?>