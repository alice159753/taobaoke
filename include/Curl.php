<?php

class Curl
{
    var $debug;
    var $errorNo;
    var $errorMessage;
    var $referer;
    var $multiTask;
    var $ch_multi;
    var $multiError;
    
    function Curl()
    {
        $this->debug = false;
        $this->referer = "";
        $this->multiTask = array();
        $this->ch_multi = null;
        $this->header = "";
        $this->timeout = 0;
        $this->cookie = null;
        $this->cookie_str = null;
    }
    
    function setHeader($header)
    {
        $this->header = $header;
    }

    function setDebug($flag)
    {
        $this->debug = $flag;
    }
    
    function setReferer($url)
    {
        $this->referer = $url;
    }

    function setTimeout($second)
    {
        $this->timeout = $second;
    }

    function setCookie($file)
    {
        $this->cookie = $file;
    }

    function setCookieStr($str)
    {
        $this->cookie_str = $str;
    }

    function doPost($url, $post)
    {
        if( !isset($post['http_sync']) ) 
        {
            $post = is_array($post) ? http_build_query($post) : $post;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip, deflate, sdch");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36');

        if ( $this->referer != "" )
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }

        if ($this->header)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        if ($this->timeout)
        {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        }

        if ($this->cookie) 
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        }

        if ($this->cookie_str) 
        {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie_str);
        }

        $content = curl_exec($ch);

        $this->errorNo      = curl_errno($ch);
        $this->errorMessage = curl_error($ch);
        
        curl_close($ch);
        
        return $content;
    }

    function doDelete($url, $data)
    {
        $data = is_array($data) ? http_build_query($data) : $data;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip, deflate, sdch");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36');

        if ( $this->referer != "" )
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }

        if ($this->header)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        if ($this->timeout)
        {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        }

        if ($this->cookie) 
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        }

        if ($this->cookie_str) 
        {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie_str);
        }

        $content = curl_exec($ch);

        $this->errorNo      = curl_errno($ch);
        $this->errorMessage = curl_error($ch);
        
        curl_close($ch);
        
        return $content;
    }

    function getContent($url, $passiveOff = false)
    {
        if ($this->debug) echo "CurlActor::curlGetContent() : $url \n";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 90); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, sdch");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36');

        if ($this->header)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        if ( $this->referer != "" )
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }

        if ($this->cookie) 
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        }

        if( $passiveOff )
        {
            curl_setopt($ch, CURLOPT_FTP_USE_EPSV, false);
        }

        if ($this->cookie_str) 
        {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie_str);
        }
            
        $content = curl_exec($ch);

        $this->errorNo      = curl_errno($ch);
        $this->errorMessage = curl_error($ch);
        
        curl_close($ch);
        
        return $content;
    }

    function getHeaders($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)');

        if ( $this->referer != "" )
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }
        
        $headers = curl_exec ($ch);

        $this->errorNo      = curl_errno($ch);
        $this->errorMessage = curl_error($ch);

        curl_close ($ch);
        
        return $headers;
    }

    function getContentLength($url)
    {
        if ($this->debug) echo "CurlActor::getContentLength() : $url \n";
        
        $headers = $this->getHeaders($url);
        
        echo $headers;
        
        $lines = explode("\n", $headers);
        
        for($i = 0; isset($lines[$i]); $i++)
        {
            if ( strstr($lines[$i], "Content-Length:") )
            {
                $contentLength = trim( str_replace("Content-Length:", "", $lines[$i]) );
                
                return $contentLength;
            }
        }
        
        return -1;
    }
    
    function getErrorNo()
    {
        return $this->errorNo;
    }
    
    function getErrorMessage()
    {
        return $this->errorMessage;
    }
    
    function isRedirect($url)
    {
        // get headers && judge if "Location: xxxx" is existed.
        
        if ($this->debug) echo "CurlActor::isRedirect() : $url \n";
        
        $headers = $this->getHeaders($url);

        $lines = explode("\n", $headers);
        
        for($i = 0; isset($lines[$i]); $i++)
        {
            if ( strstr($lines[$i], "Location:") )
            {
                $location = trim( str_replace("Location:", "", $lines[$i]) );
                
                return $location;
            }
        }
        
        return false;
    }

    function addMultiTask($url, $dataArray = "")
    {
        if ( !$this->ch_multi )
        {
            $this->ch_multi = curl_multi_init(); // multi curl handler
            $this->multiError = array();
        }

        $ch = curl_init();

        if ( $this->header )
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }
        
        curl_setopt($ch, CURLOPT_ENCODING , "gzip, deflate, sdch");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($ch, CURLOPT_MAXREDIRS, 7);

        if ( is_array($dataArray) )
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataArray));
        }
        else if ( $dataArray != "" )
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataArray);
        }

        if ($this->cookie) 
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        }

        curl_multi_add_handle($this->ch_multi, $ch); // put curl resource into multi curl handler.

        $this->multiTask[] = $ch;
    }

    function doMultiTask()
    {
        $resultArray = array();

        $this->multiError = array();

        do 
        {
            $mrc = curl_multi_exec($this->ch_multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) 
        {   
            while (curl_multi_exec($this->ch_multi, $active) === CURLM_CALL_MULTI_PERFORM);

            if (curl_multi_select($this->ch_multi) != -1) 
            {   
                do {
 
                    $mrc = curl_multi_exec($this->ch_multi, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }   
        }

        // get return
        foreach($this->multiTask as $i => $ch) 
        {
            if ( curl_errno($ch) == 0 )
            {
                $resultArray[$i] = curl_multi_getcontent($ch);
                $this->multiError[] = array("no" => 0, "message" => "");
            }
            else
            {
                $resultArray[$i] = curl_multi_getcontent($ch);
                $this->multiError[] = array("no" => curl_errno($ch), "message" => curl_error($ch));
            }
        }

        // remove all handles
        foreach($this->multiTask as $ch) 
        {
            curl_multi_remove_handle($this->ch_multi, $ch);
        }

        curl_multi_close($this->ch_multi);
        $this->multiTask = array();

        return $resultArray;
    }

    function getMultiError()
    {
        return $this->multiError;        
    }
}

?>