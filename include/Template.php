<?php

class Template 
{
    var $input;

    var $startTag;
    var $endTag;
    
    var $data;          // record all data for replace, skip, ...
    
    var $clearTag;      // if clear all tag line
    var $clearSpace;    // if clear space from head and tail of line
    var $autoBrackets;
    
    var $levelTag;      // record the last replace tag for specific level, 
                        // Example: $this->levelTag[1] = 'HOTEL'
    var $lines;
    var $content;
    var $tagCounter;

    function Template($input, $startTag = "<!-- start", $endTag = "<!-- end")
    {            
        $this->levelTag[0] = "TEMPLATE_ROOT";
        $this->levelTagCounter[0] = 0;
        
        $this->input = $input;
        
        $this->startTag = $startTag;
        $this->endTag   = $endTag;

        $this->clearTag = FALSE;
        $this->clearSpace = TRUE;
        $this->tagCounter = 0;
        $this->autoBrackets = false;
    }
    
    function setClearTag($flag)
    {
        $this->clearTag = $flag;
    }

    function setClearSpace($flag)
    {
        $this->clearSpace = $flag;
    }

    function setGlobalReplace($tag, $data)
    {
        $this->globalReplaceData[$tag] = $data;
    }

    function setAutoBrackets($flag)
    {
        $this->autoBrackets = $flag;
    }
    
    function process()
    {
        $fd = fopen($this->input, "r");
        
        while($line = fgets($fd, 4096))
        {
            if ( $this->clearSpace )
            {
                $line = trim($line);
            }
            else
            {
                $line = rtrim($line);
            }
            
            $this->lines[] = $line; // $this->globalReplace($line);
        }
        
        fclose($fd);
        
        $this->lines = $this->parse(1, $this->levelTag[0], 0, $this->lines);

        $this->content = implode("\n", $this->lines);
    }
    
    function globalReplace($line)
    {
        if ( !is_array($this->globalReplaceData) )
        {
            return $line;
        }
        
        reset($this->globalReplaceData);

        while ( list( $key , $val ) = each ($this->globalReplaceData) )
        {
            $line = str_replace($key, $val, $line);
        }
        
        return $line;
    }
    
    function setReplace($tag, $dataArray, $level = 1)
    {
        $this->tagCounter ++;
        
        $this->levelTag[$level] = $tag;
        $this->levelTagCounter[$level] = $this->tagCounter;
        
        $parentTag = $this->levelTag[$level-1];
        $parentTagCounter = $this->levelTagCounter[$level-1];

        if ( $this->autoBrackets )
        {
            $dataArray = $this->addBrackets($dataArray);
        }

        $this->replaceData[$level][$parentTag][$parentTagCounter][$tag][] = $dataArray;
        $this->selfTag[$level][$parentTag][$parentTagCounter][$tag][] = $this->tagCounter;
    }

    function setMultiReplace($tag, $dataArray, $level = 1)
    {
        for ($i = 0; isset($dataArray[$i]); $i++) 
        { 
            $this->setReplace($tag, $dataArray[$i], $level);
        }
    }

    function addBrackets($dataArray)
    {
        $resultArray = array();

        foreach ($dataArray as $key => $value) 
        {
            if ( is_numeric($key) )
            {
                continue;
            }

            $resultArray["{". $key ."}"] = $value;
        }

        return $resultArray;
    }
    
    function setReplaceSegment($tag, $data, $level = 1)
    {
        $this->tagCounter ++;
        
        $this->levelTag[$level] = $tag;
        $this->levelTagCounter[$level] = $this->tagCounter;
        
        $parentTag = $this->levelTag[$level-1];
        $parentTagCounter = $this->levelTagCounter[$level-1];

        $this->replaceSegmentData[$level][$parentTag][$parentTagCounter][$tag][] = $data;
        $this->selfTag[$level][$parentTag][$parentTagCounter][$tag][] = $this->tagCounter;
    }

    function setSkip($tag, $level = 1)
    {
        $this->tagCounter ++;

        $parentTag = $this->levelTag[$level-1];
        $parentTagCounter = $this->levelTagCounter[$level-1];

        $this->skipData[$level][$parentTag][$parentTagCounter][$tag] = TRUE;
    }

    function setNothing($tag, $level = 1)
    {
        $this->setReplace($tag, array(), $level);
    }

    function parse($level, $parentTag, $parentTagCounter, $lines)
    {
        $newLines = array();
        
        for($i = 0; isset($lines[$i]); $i++)
        {
            if ( !($tag = $this->getStartTag($lines[$i])) )
            {
                $newLines[] = $lines[$i];
                continue;
            }
            
            $blockLines = NULL;
            
            $startLine = $lines[$i];
            
            for($i++; isset($lines[$i]); $i++)
            {
                if ( $this->isEndTag($lines[$i], $tag) )
                {
                    $endLine = $lines[$i];
                    break;
                }
                
                $blockLines[] = $lines[$i];
            }

            $tempLines = NULL;

            if ( isset($this->replaceSegmentData[$level][$parentTag][$parentTagCounter][$tag][0]) )
            {
                $tempLines = explode("\n", $this->replaceSegmentData[$level][$parentTag][$parentTagCounter][$tag][0]);
                $newLines = array_merge($newLines, $tempLines);
            }
            else if ( isset($this->replaceData[$level][$parentTag][$parentTagCounter][$tag][0]) )
            {
                for($j = 0; isset($this->replaceData[$level][$parentTag][$parentTagCounter][$tag][$j]); $j++)
                {
                    $tempLines = $this->parse($level+1, 
                                              $tag, 
                                              $this->selfTag[$level][$parentTag][$parentTagCounter][$tag][$j], 
                                              $blockLines); 
                                                                      
                    $tempContent = implode("\n", $tempLines);
                    
                    reset($this->replaceData[$level][$parentTag][$parentTagCounter][$tag][$j]);
                    
                    while ( list( $key , $val ) = each ($this->replaceData[$level][$parentTag][$parentTagCounter][$tag][$j]) )
                    {
                        $tempContent = str_replace($key, $val, $tempContent);
                    }
                    
                    $tempLines = explode("\n", $tempContent);
                    
                    // clear tag 
                    if ( !$this->clearTag )
                    {
                        $newLines[] = $startLine;
                        $newLines = array_merge($newLines, $tempLines);
                        $newLines[] = $endLine;
                    }
                    else
                    {
                        $newLines = array_merge($newLines, $tempLines);
                    }
                }
            }
            else if ( isset($this->nothingData[$level][$parentTag][$parentTagCounter][$tag]) &&
                      $this->nothingData[$level][$parentTag][$parentTagCounter][$tag] == TRUE )
            {
                // do nothing
                $tempLines = $this->parse($level+1, $tag, $this->selfTag[$level][$parentTag][$parentTagCounter][$tag], explode("\n", $blockContent)); 
            }
            else if ( isset($this->skipData[$level][$parentTag][$parentTagCounter][$tag]) &&
                      $this->skipData[$level][$parentTag][$parentTagCounter][$tag] == TRUE )
            {
                // skip data
                unset($tempLines);
            }
            else
            {
                // default ==> skip data
                unset($tempLines);
            }
        }
        
        return $newLines;
    }

    function getStartTag($line)
    {
        $tokens = explode($this->startTag, $line);
        
        if ( !isset($tokens[1]) )
        {
            return NULL;
        }
        
        $tokens = explode(" ", $tokens[1]);
        
        return (isset($tokens[1]) ? $tokens[1] : NULL);
    }

    function isEndTag($line, $tag)
    {
        $tokens = explode($this->endTag, $line);
        
        if ( !isset($tokens[1]) )
        {
            return FALSE;
        }
        
        $tokens = explode(" ", $tokens[1]);
        
        if ( isset($tokens[1]) && $tokens[1] == $tag )
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    function getContent()
    {
        return $this->content;
    }
    
    function debug()
    {
        echo "[replaceData]\n";
        print_r($this->replaceData);

        echo "[selfTag]\n";
        print_r($this->selfTag);
    }

    function output()
    {
        echo $this->getContent();
    }
    
    function alertAndBack($message, $history = -1)
    {
        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "window.history.go(". $history .");\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    function alertAndRedirect($message, $url)
    {
        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "window.location.href='$url';\n";
        echo "//-->\n";
        echo "</script>\n";
    }
}

?>