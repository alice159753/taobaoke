<?php

class JavaScript
{
    static function charset($charset)
    {
//            echo "<head>";
//            echo "<META http-equiv=Content-Type content=\"text/html; charset=".$charset."\">";
//            echo "</head>";
    }

	static function alert($message)
	{
        header("Content-type: text/html; charset=utf-8");

        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function closeWindow()
    {
    	echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "window.close();\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function alertAndBack($message, $history = -1)
    {
        header("Content-type: text/html; charset=utf-8");
        
        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "window.history.go(". $history .");\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function back($history = -1)
    {
        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "window.history.go(". $history .");\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function alertAndReplace($message, $url)
    {
        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "window.location.replace('".$url."');";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function replace($url)
    {
        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "window.location.replace('".$url."');";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function alertAndRedirect($message, $url)
    {
        header("Content-type: text/html; charset=utf-8");

        echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "window.location.href='$url';\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function redirect( $url )
    {
    	echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "window.location.href='$url';\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function alertAndClose($message)
    {
    	echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "alert('".$message."');\n";
        echo "window.close();\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function close()
    {
    	echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        echo "window.close();\n";
        echo "//-->\n";
        echo "</script>\n";
    }

    static function delayToRedirect( $url, $milliseconds=0 )
    {
    	echo "<script language='JavaScript'>\n";
        echo "<!--\n";
        if( $milliseconds == 0 )
        {
        	echo "window.location.href='$url';\n";
        }
        else if( $milliseconds > 0 )
        {
    		echo "window.setTimeout( \"window.location.href='$url';\", ".$milliseconds." )\n";
    	}
    	echo "//-->\n";
        echo "</script>\n";
    }

    static function openWindow( $file )
    {
    	echo "<script language='JavaScript'>\n";
    	echo "<!--\n";
    	echo "window.open( '$file' );\n";
    	echo "//-->\n";
    	echo "</script>\n";
    }

    static function openVariableWindow( $parameters )
    {
    	$file       = $parameters['FILE'];
    	$name       = $parameters['NAME'];
    	$height     = $parameters['HEIGHT'];
    	$width      = $parameters['WIDTH'];
    	$top        = $parameters['TOP'];
    	$left       = $parameters['LEFT'];
    	$toolbar    = isset( $parameters['TOOLBAR'] ) ? $parameters['TOOLBAR'] : 'no';
    	$menubar    = isset( $parameters['MENUBAR'] ) ? $parameters['MENUBAR'] : 'no';
    	$scrollbars = isset( $parameters['SCROLLBARS'] ) ? $parameters['SCROLLBARS'] : 'no';
    	$resizable  = isset( $parameters['RESIZABLE'] ) ? $parameters['RESIZABLE'] : 'no';
    	$location   = isset( $parameters['LOCATION'] ) ? $parameters['LOCATION'] : 'no';
    	$status     = isset( $parameters['STATUS'] ) ? $parameters['STATUS'] : 'no';

    	echo "<script language='JavaScript'>\n";
    	echo "<!--\n";
    	echo "window.open( '".$file."', '".$name."', 'height=".$height.", width=".$width.
    	     ", top=".$top.", left=".$left.", toolbar=".$toolbar.
    	     ", menubar=".$menubar.", scrollbars=".$scrollbars.
    	     ", resizable=".$resizable.", location=".$location.", status=".$status."' );\n";
    	echo "//-->\n";
    	echo "</script>\n";
    }

    static function reflashParentWindow()
    {
    	echo "<script language='JavaScript'>\n";
    	echo "<!--\n";
    	echo "window.opener.location.reload();\n";
    	echo "//-->\n";
    	echo "</script>\n";
    }
}

?>