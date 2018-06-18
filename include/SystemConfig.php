<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class SystemConfig extends Table
	{
	    function SystemConfig($myMySQL, $table = "system_config")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getData($row)
        {
            $dataArray = array();
            $dataArray["{no}"]               = $row['no'];
            $dataArray["{site_name}"]        = $row['site_name'];
            $dataArray["{site_title}"]       = $row['site_title'];
            $dataArray["{site_keyword}"]     = $row['site_keyword'];
            $dataArray["{site_description}"] = $row['site_description'];
            $dataArray["{site_logo}"]        = $row['site_logo'];
            $dataArray["{comany_address}"]   = $row['comany_address'];
            $dataArray["{phone}"]            = $row['phone'];
            $dataArray["{qq}"]               = $row['qq'];
            $dataArray["{skype}"]            = $row['skype'];
            $dataArray["{wechat}"]           = $row['wechat'];
            $dataArray["{email}"]            = $row['email'];
            $dataArray["{add_time}"]         = $row['add_time'];
            $dataArray["{update_time}"]      = $row['update_time'];

            return $dataArray;
        }

	}

?>