<?php

class Logs
{
    private static $start_time;
    private static $echo_method;

    function Logs()
    {
    }

    static function makeHeader($flag)
    {
        $header = "";

        if ( strstr($flag, "t") )
        {
            $header .= date("[Y-m-d H:i:s]");
        }

        if ( strstr($flag, "T") )
        {
            $header .= date("[H:i:s]");
        }
    
        if ( strstr($flag, "p") )
        {
            $process_time = self::getProcessTime();
            $header .= "(ptime: $process_time)";
        }

        if ( strstr($flag, "P") )
        {
            $header .= "(pid: ". getmypid() .")";
        }

        return $header;
    }

    static function makeContent($data, $flag = "")
    {
        if ( is_array($data) )
        {
            if ( strstr($flag, "j") )
            {
                return json_encode($data) ."\n";
            }
            elseif( strstr($flag, "s") )
            {
                return serialize($data) ."\n";
            }
            else
            {
                return print_r($data, true);
            }
        }

        return $data;
    }

    static function write($filename, $data, $flag = "", $note = "")
    {
        self::writeFile($filename, $data, $flag, $note);
    }

    static function writeFile($filename, $data, $flag = "", $note = "")
    {
        if ( !file_exists(LOGS_DIR ."/". $filename) )
        {
            $pathArray = pathinfo(LOGS_DIR ."/". $filename);

            if ( !file_exists($pathArray['dirname']) )
            {
                mkdir($pathArray['dirname'], 0700, true);
            }
        }

        $header = self::makeHeader($flag);
        $content = self::makeContent($data, $flag);

        if ( strstr($flag, "f") )
        {
            $content_filename = LOGS_DIR ."/". $filename .".". substr(uniqid(), -8) .".txt";
            
            file_put_contents($content_filename, $content);

            if ($header != "") 
            {
                $header .= " ";
            }

            if ( $note != "" )
            {
                $header .= $note ." ";
            }

            $pathArray = pathinfo($content_filename);

            $content_file_info = strlen($content);

            if ( is_array($data) )
            {
                $content_file_info .= ":". count($data);
            }

            $content =  $pathArray["basename"] ." (". $content_file_info .")\n";

            file_put_contents(LOGS_DIR ."/". $filename, $header . $content, FILE_APPEND);
        }
        else
        {
            if ( $header != "" )
            {
                $header .= strstr($flag, "n") ? "\n" : " ";
            }

            if ( strstr($flag, "N") && substr($content, -1, 1) != "\n" )
            {
                $content .= "\n";
            }

            file_put_contents(LOGS_DIR ."/". $filename, $header . $content, FILE_APPEND);
        }
    }

    static function setStartTime($start_time = "")
    {
        self::$start_time = $start_time == "" ? microtime() : $start_time;
    }

    static function getProcessTime()
    {
        list($usec, $sec) = explode(" ", self::$start_time);
        $start_time = (float)$usec + (float)$sec;

        list($usec, $sec) = explode(" ", microtime());
        $end_time = (float)$usec + (float)$sec;

        return ($end_time - $start_time);
    }

    static function rmExpiredFile($dir, $expired_day, $keyword = '')
    {
        $expired_time = date("Y-m-d H:i:s", time() - $expired_day*24*60*60);

        self::rmFile($dir, $expired_time, $keyword);
    }

    static function rmFile($dir, $expired_time, $keyword)
    {
        $d = dir($dir);
        
        while (false !== ($entry = $d->read())) 
        {
            if( $entry != '.' && $entry != '..' ) 
            {
                $entry = $dir.'/'.$entry;
    
                if( is_dir($entry) ) 
                {
                    self::rmFile($entry, $expired_time, $keyword);

                    if ( self::isEmptyDirectory($entry) )
                    {
                        rmdir($entry);
                    }
                } 
                else 
                {
                    $pathInfo = pathinfo($entry);

                    if ( $keyword != "" && !strstr($pathInfo["basename"], $keyword) )
                    {
                        continue;
                    }

                    $filemtime = date("Y-m-d H:i:s", filemtime($entry));
                    
                    if ( $filemtime <= $expired_time )
                    {
                        echo "entry=$entry, expired_time=$expired_time, filemtime=$filemtime, keyword=$keyword\n";
                        
                        unlink($entry);

                        usleep(1000);
                    }
                }
            }
        }
    
        $d->close();
    }

    static function isEmptyDirectory($dir)
    {
        if ( !is_dir($dir) )
        {
            return false;
        }

        $d = dir($dir);

        while (false !== ($entry = $d->read())) 
        {
            if($entry != '.' && $entry != '..') 
            {
                return false;
            }
        }

        return true;
    }

    static function compressFile($filename, $method = 'tar', $flag = '')
    {
        $pathArray = pathinfo($filename);
        $dir = $pathArray['dirname'];
        $dir = str_replace("\\", "/", $dir);
        $basename = $pathArray['basename'];
        $extension = $pathArray['extension'];

        $method = empty($method) ? 'tar' : $method;

        switch ($method) 
        {
            case 'tar':
                system("cd $dir; tar zcvf $basename.tgz $basename");
                
                break;
            
            default:
                echo 'no method';
                break;
        }

        if( strstr($flag, 'd') )
        {
            system("cd $dir; rm -rf $basename"); 
        }
    }

    static function syncFile($filename, $ftp, $method = 'curl', $flag = '')
    {
        $myCurl = new Curl();

        $pathArray = pathinfo($filename);
        $dir = $pathArray['dirname'];
        $dir = str_replace("\\", "/", $dir);
        $basename = $pathArray['basename'];
        $extension = $pathArray['extension'];

        $method = empty($method) ? 'curl' : $method;

        switch ($method) 
        {
            case 'curl':
                system("cd $dir; /usr/bin/curl --ftp-create-dirs -T  $basename $ftp/ -k --ftp-ssl");
                
                break;
            
            default:
                echo 'no method';
                break;
        }

        if( strstr($flag, 'd') )
        {
            //check filesize
            $local_size = filesize($filename);
            $remote_size = $myCurl->getContentLength("$ftp/$basename");

            echo "local_size=$local_size, remote_size=$remote_size";

            if( $local_size == $remote_size )
            {
                system("cd $dir; rm -rf $basename"); 
            }
        }
    }

    static function writeMySQL($myObject, $dataArray)
    {
        $myObject->addRow($dataArray);

        return $myObject->getInsertId();
    }

    static function readFile($filename, $flag = "")
    {
        $resultArray = array();

        $pathArray = pathinfo($filename);

        $dir = $pathArray['dirname'];
        $dir = str_replace("\\", "/", $dir);
        $basename = $pathArray['basename'];
        $extension = $pathArray['extension'];

        if( !is_dir($filename) ) 
        {
            $data = file_get_contents($filename);

            $data = self::parseContent($data, $flag);

            $resultArray[$basename] = $data;

            return $resultArray;
        }
        else
        {
            $dh = opendir($filename);

            while (($file = readdir($dh)) !== false) 
            {
                if( $file == '.' || $file == '..' ) 
                {
                    continue;
                }

                $data = file_get_contents($filename."/".$file);

                $data = self::parseContent($data, $flag);

                $resultArray[$file] = $data;
            } 

            closedir($dh);
        }

        return $resultArray;
    }

    static function parseContent($data, $flag)
    {
        if ( strstr($flag, "j") )
        {
            return json_decode($data, true);
        }
        elseif( strstr($flag, "s") )
        {
            return unserialize($data);
        }

        return $data;
    }

    static function setEchoMethod($method)
    {
        self::$echo_method = $method;
    }

    static function echoBase($message, $status)
    {
        $echo_method = empty( self::$echo_method ) ? 'echo' : self::$echo_method;

        switch (self::$echo_method) 
        {
            case 'echo':
                echo "[$status]: $message\n";
                break;

            case 'write_file':
                $content = "[$status]: $message\n";
                file_put_contents(LOGS_DIR."/logs_".date('Ymd').".log", $content, FILE_APPEND);
                break;

            case 'null':
                break;

            default:
                break;
        }
    }

    static function error($message)
    {
        self::echoBase($message, 'ERROR');
    }

    static function debug($message)
    {
        self::echoBase($message, 'DEBUG');
    }

    static function warn($message)
    {
        self::echoBase($message, 'WARNING');
    }

    static function info($message)
    {
        self::echoBase($message, 'INFO');
    }

    static function fatal($message)
    {
        self::echoBase($message, 'FATAL');
    }

    static function trace($message)
    {
        self::echoBase($message, 'TRACE');
    }


}

Logs::setStartTime();

// static function write() 
// [flag usage]
// p: (header) process time
// P: (header) process id
// t: (header) datetime
// T: (header) time
// n: (header) new line after header if header is not empty.
// N: new line after content if end char of content is not "\n".
// j: array will be transfered to json formate
// s: array will be transfered by serialize()
// f: array will be stored into txt file (named filename + unique_id + ."txt")
// [data usage]
// normal value or array (will be transfered by print_r() [default])
// note is useful when content would be stored as file.


//date format date('Ymd')



?>