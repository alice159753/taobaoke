<?php


    class WeChat
    {
        
        public function responseFirstCheck()
        {
            $echoStr = $_GET["echostr"];

            if( !empty($echoStr) )
            {
                echo $echoStr;
                exit;
            }
        }

        public function checkSignature($token)
        {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );

            return $tmpStr == $signature ? true : false;
        }

        public function login($app_id, $redirect_uri)
        {
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?".
                                          "appid=$app_id&redirect_uri=$redirect_uri".
                                          "&response_type=code&scope=snsapi_base#wechat_redirect";

            header('location:'.$url);

        }

        public function getToken($appid, $appsecret)
        {
            $myCurl = new Curl();
            //$mySSDB = new SimpleSSDB(SSDB_HOST, SSDB_PORT);

            //$ssdb_key = "weixin:access_token:$appid";
            //$access_token = $mySSDB->get($ssdb_key);

            $access_token = file_get_contents("access_token.log");
            $access_token = json_decode($access_token, true);

            if( !empty($access_token) && strtotime($access_token['make_time']) > strtotime('now') )
            {
                return $access_token['access_token'];
            }

            $url = 'https://api.weixin.qq.com/cgi-bin/token';

            $dataArray = array();
            $dataArray['grant_type'] = 'client_credential';
            $dataArray['appid'] = $appid;
            $dataArray['secret'] = $appsecret;

            $result = $myCurl->doPost($url, $dataArray);
            $result = json_decode($result, true);

            $result['make_time'] = date('Y-m-d H:i:s', strtotime('-1 hour'));

            file_put_contents('access_token.log', json_encode($result));

            //$mySSDB->setx($ssdb_key, $result['access_token'], 7000);

            return $result['access_token'];
        }

        public function makeMenu($token, $dataArray)
        {
            $myCurl = new Curl();

            $dataArray = json_encode($dataArray, JSON_UNESCAPED_UNICODE);

            $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";

            $result = $myCurl->doPost($url, $dataArray);
            $result = json_decode($result, true);

            return $result;
        }

        public function deleteMenu($token)
        {
            $myCurl = new Curl();

            $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=$token";

            $result = $myCurl->doPost($url, '');
            $result = json_decode($result, true);

            return $result;
        }

        public function responseText($to_username, $from_username, $text)
        {       
            $myTemplate = new Template(TEMPLATE_DIR ."/wechat_response_text.html");
            $myTemplate->setClearTag(true);

            $dataArray = array();
            $dataArray["{ToUserName}"]   = $to_username;
            $dataArray["{FromUserName}"] = $from_username;
            $dataArray["{CreateTime}"]   = time();
            $dataArray["{Content}"]      = $text;

            $myTemplate->setReplace("text", $dataArray);

            $myTemplate->process();
            return  $myTemplate->getContent();
        }

        public function getParseReceive()
        {   
            $resultArray = array();

            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            $resultArray['FromUserName'] = $postObj->FromUserName;
            $resultArray['ToUserName']   = $postObj->ToUserName;
            $resultArray['CreateTime']   = $postObj->CreateTime;
            $resultArray['MsgType']      = $postObj->MsgType;
            $resultArray['MsgId']        = $postObj->MsgId;
            $resultArray['MediaId']      = $postObj->MediaId;

            //text
            $resultArray['Content'] = $postObj->Content;

            //image
            $resultArray['PicUrl']  = $postObj->PicUrl;

            //voice
            $resultArray['Format'] = $postObj->Format;

            //video
            $resultArray['ThumbMediaId'] = $postObj->ThumbMediaId;

            //shortvideo

            //location
            $resultArray['Location_X'] = $postObj->Location_X;
            $resultArray['Location_Y'] = $postObj->Location_Y;
            $resultArray['Scale']      = $postObj->Scale;
            $resultArray['Label']      = $postObj->Label;

            //link
            $resultArray['Title']       = $postObj->Title;
            $resultArray['Description'] = $postObj->Description;
            $resultArray['Url']         = $postObj->Url;

            //event
            $resultArray['Event'] = $postObj->Event;   //subscribe or unsubscribe

            //scan
            $resultArray['EventKey'] = $postObj->EventKey;  
            $resultArray['Ticket']   = $postObj->Ticket;  // qrscene_ 

            //report the location events
            $resultArray['Latitude']  = $postObj->Latitude;  
            $resultArray['Longitude'] = $postObj->Longitude;  
            $resultArray['Precision'] = $postObj->Precision;  

            //menu

            //speech recognition
            $resultArray['Recognition'] = $postObj->Recognition;

            //template
            $resultArray['Status'] = $postObj->Status;


            //mass send  job finish
            $resultArray['TotalCount']  = $postObj->TotalCount;
            $resultArray['FilterCount'] = $postObj->FilterCount;
            $resultArray['SentCount']   = $postObj->SentCount;
            $resultArray['ErrorCount']  = $postObj->ErrorCount;

            return $resultArray;
        }

        //登录时根据code获取openid
        function getAuthorization($appid, $appsecret, $code)
        {
            $myCurl = new Curl();

            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';

            $dataArray = array();
            $dataArray['grant_type'] = 'authorization_code';
            $dataArray['code'] = $code;
            $dataArray['appid'] = $appid;
            $dataArray['secret'] = $appsecret;

            $result = $myCurl->getContent($url."?". http_build_query($dataArray));
            $result = json_decode($result, true);

            // { 
            //     "access_token":"ACCESS_TOKEN", 
            //     "expires_in":7200, 
            //     "refresh_token":"REFRESH_TOKEN",
            //     "openid":"OPENID", 
            //     "scope":"SCOPE" 
            // }
        
            // [access_token] => i1pSmeDh3ZaQAPRIjXzwiLWsvVkn1D6Wesf28N7kt5ganpdXjmT7tL072bW-a_Jj8S52dNRM-l3F3LnYeQPBbkbkC8CqiiHktOqSDofj5xo
            // [expires_in] => 7200
            // [refresh_token] => uHl3QGTZNH4NixFkX7ovziIaWEg7YtbO83Xoa29icIYrP0BA8CVCrbNm1mZuKYCPi3p7CGsMon6Dc-EBSWJzQkheBIYD9barQlFecANz8ww
            // [openid] => ohrAb1s2Sa531m0Zs0WEcX8KCXCs
            // [scope] => snsapi_login
            // [unionid] => ocj5w1KRtN61KXJvxHLLufRi9mn0

            // [errcode] => 40163
            // [errmsg] => code been used, hints: [ req_id: Po0253th21 ]

            return $result;
        }


        //获取用户信息
        function getUser($access_token, $openid)
        {
            $myCurl = new Curl();

            $url = 'https://api.weixin.qq.com/sns/userinfo';

            $dataArray = array();
            $dataArray['access_token'] = $access_token;
            $dataArray['openid'] = $openid;

            $result = $myCurl->getContent($url."?". http_build_query($dataArray));
            $result = json_decode($result, true);

            // Array
            // (
            //     [openid] => ohrAb1s2Sa531m0Zs0WEcX8KCXCs
            //     [nickname] => 王春
            //     [sex] => 2
            //     [language] => zh_CN
            //     [city] => Chengdu
            //     [province] => Sichuan
            //     [country] => CN
            //     [headimgurl] => http://wx.qlogo.cn/mmhead/Q3auHgzwzM7pHRhZeM1jlmnReiaCbgWD25vQgMOuNkIXpbmbtuSZNHQ/0
            //     [privilege] => Array
            //         (
            //         )

            //     [unionid] => ocj5w1KRtN61KXJvxHLLufRi9mn0
            // )

            return $result;
        }

        function getUser2($access_token, $openid)
        {
            $myCurl = new Curl();

            $url = 'https://api.weixin.qq.com/cgi-bin/user/info';

            $dataArray = array();
            $dataArray['access_token'] = $access_token;
            $dataArray['openid'] = $openid;

            $result = $myCurl->getContent($url."?". http_build_query($dataArray));
            $result = json_decode($result, true);

            // Array
            // (
            //     [openid] => ohrAb1s2Sa531m0Zs0WEcX8KCXCs
            //     [nickname] => 王春
            //     [sex] => 2
            //     [language] => zh_CN
            //     [city] => Chengdu
            //     [province] => Sichuan
            //     [country] => CN
            //     [headimgurl] => http://wx.qlogo.cn/mmhead/Q3auHgzwzM7pHRhZeM1jlmnReiaCbgWD25vQgMOuNkIXpbmbtuSZNHQ/0
            //     [privilege] => Array
            //         (
            //         )

            //     [unionid] => ocj5w1KRtN61KXJvxHLLufRi9mn0
            // )

            return $result;
        }

        //微信支付
        function h5($body, $flower, $total_fee, $notify_url, $return_url)
        {
            $myMySQL = new MySQL();
            $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            $myCurl = new Curl();
            $myPayOrder = new PayOrder($myMySQL);

            $merchant_number = MERCHANT_NUMBER;

            $out_trade_no = $myPayOrder->createOrder($flower, $total_fee, 2);

            $dataArray = array();
            $dataArray['appid']            = WEIXIN_PAY_APPID;
            $dataArray['mch_id']           = MERCHANT_NUMBER;
            $dataArray['nonce_str']        = $this->great_rand();
            $dataArray['body']             = $body;
            $dataArray['out_trade_no']     = $out_trade_no;
            $dataArray['total_fee']        = $total_fee;
            $dataArray['spbill_create_ip'] = $this->get_client_ip();
            $dataArray['notify_url']       = $notify_url;
            $dataArray['return_url']       = $return_url;
            $dataArray['trade_type']       = 'MWEB'; //以分为单位

            $sign = $this->get_sign($dataArray);
            $dataArray['sign'] = $sign;

            $postXml = $this->arrayToXml($dataArray);

            $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

            $xml = $myCurl->doPost($url, $postXml);

            $content = simplexml_load_string($xml);

            $mweb_url = $content->mweb_url;

            return  $mweb_url;
        }

        function get_client_ip()
        {
            $cip = "unknown";

            if( $_SERVER['REMOTE_ADDR'] )
            {
                $cip = $_SERVER['REMOTE_ADDR'];
            }
            else if( getenv('REMOTE_ADDR') )
            {
                $cip = getenv('REMOTE_ADDR');
            }

            return $cip;
        }
        
        function get_sign($dataArray)
        { 
            ksort($dataArray);

            $unSignParaString = $this->formatQueryParaMap($dataArray, false);

            $unSignParaString = $unSignParaString."&key=".WEIXIN_PAY_KEY; //要在后台管理设定32位的字符
            
            return strtoupper(md5($unSignParaString));
        }

        function formatQueryParaMap($paraMap, $urlencode)
        {
            $buff = "";

            ksort($paraMap);

            foreach ($paraMap as $k => $v)
            {
                if (null != $v && "null" != $v && "sign" != $k) 
                {
                    if( $urlencode )
                    {
                       $v = urlencode($v);
                    }

                    $buff .= $k . "=" . $v . "&";
                }
            }

            $reqPar;

            if (strlen($buff) > 0) 
            {
                $reqPar = substr($buff, 0, strlen($buff)-1);
            }

            return $reqPar;
        }

        function arrayToXml($arr)
        {
            $xml = "<xml>";

            foreach ($arr as $key=>$val)
            {
                 if (is_numeric($val))
                 {
                    $xml.="<".$key.">".$val."</".$key.">"; 

                 }
                 else{
                    $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
                 } 
            }

            $xml .= "</xml>";

            return $xml; 
        }

        function curl_post_ssl($url, $vars, $second=30, $aHeader=array())
        {
            $ch = curl_init();
            //超时时间
            curl_setopt($ch,CURLOPT_TIMEOUT,$second);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
            //这里设置代理，如果有的话
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);  
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml;charset=\"UTF-8\"'));

            //cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERT, getcwd()."/cert/apiclient_cert.pem");
            curl_setopt($ch,CURLOPT_SSLKEY,  getcwd()."/cert/apiclient_key.pem");
            curl_setopt($ch,CURLOPT_CAINFO, getcwd()."/cert/rootca.pem");

            if( count($aHeader) >= 1 ){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
            }
         
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
            $data = curl_exec($ch);

            if($data){
                curl_close($ch);
                return $data;
            }
            else { 
                $error = curl_errno($ch);
                curl_close($ch);
                return false;
            }
        }

        function great_rand()
        {
            $str = '1234567890abcdefghijklmnopqrstuvwxyz';
            $t1 = "";
            for($i = 0; $i < 30; $i++)
            {
                $j = rand(0,35);

                $t1 .= $str[$j];
            }
            return $t1;    
        }


        function createNonceStr($length = 16) 
        {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $str = "";

            for ($i = 0; $i < $length; $i++) 
            {
              $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }

            return $str;
        }


        function getSignPackage() 
        {
            $jsapiTicket = $this->getJsApiTicket();

            // 注意 URL 一定要动态获取，不能 hardcode.
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $timestamp = time();
            $nonceStr = $this->createNonceStr();

            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

            $signature = sha1($string);

            $signPackage = array(
              "{appId}"     => WEIXIN_APPID,
              "{nonceStr}"  => $nonceStr,
              "{timestamp}" => $timestamp,
              "{url}"       => $url,
              "{signature}" => $signature,
              "{rawString}" => $string
            );

            return $signPackage; 
        }


        function getJsApiTicket() 
        {
            $myCurl = new Curl();

            // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
            $data = json_decode(file_get_contents("jsapi_ticket.php"));
            if ($data->expire_time < time()) 
            {
              $accessToken = $this->getToken(WEIXIN_APPID, WEIXIN_APPSECRECT);
              // 如果是企业号用以下 URL 获取 ticket
              // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
              $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
              $res = json_decode($myCurl->getContent($url));
              $ticket = $res->ticket;

              if ($ticket) 
              {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                file_put_contents("jsapi_ticket.php", json_encode($data));
               }
            } 
            else 
            {
                $ticket = $data->jsapi_ticket;
            }

            return $ticket;
        }
        

    }





?>