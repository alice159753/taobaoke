<?php

class Output 
{

    public static function succ($msg, $data = array())
    {
        Output::message(0, $msg, $data);
    }

    public static function error($msg, $data = array(), $code = 1) 
    {
        Output::message($code, $msg, $data);  
    }

    private static function message($code, $msg, $data)
    {
        $result = array();

        $format = 'json';

        // 依据不同格式选择性输出
        switch ($format) 
        {
            case 'json' :
                $result['result']['status']['code'] = $code;
                $result['result']['status']['msg'] = $msg;
                $result['result']['data'] = $data;

                $result = json_encode($result);

                break;
        }

        echo $result;

        exit;
    }

        

}




?>