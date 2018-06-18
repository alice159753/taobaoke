<?php

	include_once("Table.php");


	class UserIntegral extends Table
	{
	    function UserIntegral($myMySQL, $table = "user_integral")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getData($row)
        {
            $dataArray = array();
            $dataArray['{no}']          = $row['no'];
            $dataArray['{user_no}']     = $row['user_no'];
            $dataArray['{title}']       = $row['title'];
            $dataArray['{integral}']    = $row['integral'];
            $dataArray['{add_time}']    = $row['add_time'];
            $dataArray['{update_time}'] = $row['update_time'];
            $dataArray['{note}']        = $row['note'];

            return $dataArray;
        }

	}

?>