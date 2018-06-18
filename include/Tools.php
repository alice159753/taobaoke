<?php

class Tools
{

    static function makeUnique($num = 32)
    {
        $char = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
                      "a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
                      "1","2","3","4","5","6","7","8","9","0","-","_");
        shuffle($char);

        $char = implode('', $char);

        $char = substr($char, 0, $num);

        return $char;
    }

    static function makeUniqueNumber($num = 32)
    {
        $char = array("1","2","3","4","5","6","7","8","9","0");
        shuffle($char);

        $char = implode('', $char);

        $char = substr($char, 0, $num);

        return $char;
    }

    //将一个月分成几周
    function monthSeparatorWeek($year, $month)
    {
        $result = array();

        $first_day = date("$year-$month-01");
        $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));

        $first_week = date("w", strtotime($first_day));

        $day1 = date("Y-m-d", strtotime($first_day) - $first_week*24*3600);
        $day2 = date("Y-m-d", strtotime($first_day) + (7-$first_week -1)*24*3600);

        $dayList = Tools::getDayList($day1, $day2);

        $result[] = $dayList;

        $dayList = array();
        for($i = strtotime("$day2 +1 day"); $i <= strtotime($last_day); $i = $i + 24*3600 )
        {
            $dayList[] = date('Y-m-d', $i);

            //星期六
            if( date('w', $i) == 6 )
            {
                $result[] = $dayList;
                $dayList = array();
            }
        }

        if( !empty($dayList) )
        {
            $last_day = date('Y-m-d', strtotime("$last_day next week -2 day"));

            $dayList = Tools::getDayList($dayList[0], $last_day);

            $result[] = $dayList;
        }

        return $result;
    }

    //将一个月分成几周
    function monthSeparatorWeekList($year, $month)
    {
        $result = array();
        $list = Tools::monthSeparatorWeek($year, $month);

        foreach ($list as $index => $itemList) 
        {
           for($i = 0; isset($itemList[$i]); $i++)
           {
                $result[] = $itemList[$i];
           }
        }

        return $result;
    }

    function getDayList($start_day, $end_day)
    {
        $result = array();

        for($i = strtotime($start_day); $i <= strtotime($end_day); $i = $i + 24*3600 )
        {
            $result[] = date('Y-m-d', $i);
        }

        return $result;
    }

    //7月8日 上午10:47
    function convertDate1($time)
    {
        $hour = date('h', strtotime($time)); 
        $hour = intval($hour);

        $hour_title = $hour <= 12 ? '上午' : '下午';

        return date("m月d日 $hour_title H:i", strtotime($time));
    }


    //昨天 10:47, 星期一 10:47 , 8月15日 10:47
    function convertDate2($time)
    {
        $hour = date('h', strtotime($time)); 
        $hour = intval($hour);

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $before7 = date('Y-m-d', strtotime("-7 day"));

        $pre = date('m月d日', strtotime($time));

        if( date('Y-m-d', strtotime($time)) ==  $yesterday )
        {
            $pre = '昨天';
        }
        else if( date('Y-m-d', strtotime($time)) >= $before7 )
        {
            $weekMap = array(0=>'星期天',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六');

            $w = date('w', strtotime($time));

            $pre = $weekMap[$w];
        }

        return $pre ." ".date("H:i", strtotime($time));
    }

    //根据html获得图片list
    function makeImageLists($html, $http = 'http')
    {
        $parrent = "/(href|src)=([\"|']?)([^\"'>]+.(jpg|JPG|jpeg|JPEG|gif|GIF|png|PNG))/i";
        preg_match_all($parrent, $html, $matchs);

        $imageList = $matchs[0];

        for($i = 0; isset($imageList[$i]); $i++)
        {
            $imageList[$i] = str_replace("src=\"", "", $imageList[$i]);

            $imageList[$i] = str_replace("href=\"", "", $imageList[$i]);

            $imageList[$i] = trim($imageList[$i]);

            if( !strstr($imageList[$i], 'http') && !strstr($imageList[$i], 'https') )
            {
                $imageList[$i] = $http.":".$imageList[$i];
            }
        }

        return $imageList;
    }

    function is_weixin()
    {
        if ( isset($_SERVER['HTTP_USER_AGENT'])&&strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    } 


    function getPageUrl() 
    {
        $pageURL = 'http';
        
        if ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
        {
            $pageURL .= "s";
        }
        else if ( isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" ) 
        {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ( isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" ) 
        {
            $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } 
        else 
        {
            $pageURL .= $_SERVER['HTTP_HOST'] .'/'. $_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }

}




?>