<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class Search extends Table
	{
	    function Search($myMySQL, $table = "search")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getIsFinish()
        {
            return array('0' => '未开始', '1' => '开始', '2' => '结束');
        }

        function getData($row)
        {
            $finishMap = $this->getIsFinish();

            $dataArray = array();
            $dataArray['{no}']              = $row['no'];
            $dataArray['{user_no}']         = $row['user_no'];
            $dataArray['{title}']           = $row['title'];
            $dataArray['{add_time}']        = $row['add_time'];
            $dataArray['{update_time}']     = $row['update_time'];
            $dataArray['{is_finish_title}'] = $finishMap[ $row['is_finish'] ];

            return $dataArray;
        }

        function getDataClean($row)
        {
            $finishMap = $this->getIsFinish();

            $dataArray = array();
            $dataArray['no']              = $row['no'];
            $dataArray['user_no']         = $row['user_no'];
            $dataArray['title']           = $row['title'];
            $dataArray['add_time']        = $row['add_time'];
            $dataArray['update_time']     = $row['update_time'];
            $dataArray['is_finish_title'] = $finishMap[ $row['is_finish'] ];

            return $dataArray;
        }
	}

?>