<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class ProjectTable extends Table
	{
	    function ProjectTable($myMySQL, $table = "project_table")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getData($row)
        {
            $dataArray = array();
            $dataArray['{no}']            = $row['no'];
            $dataArray['{project_no}']    = $row['project_no'];
            $dataArray['{table_name}']    = $row['table_name'];
            $dataArray['{table_display}'] = $row['table_display'];
            $dataArray['{add_time}']      = $row['add_time'];
            $dataArray['{update_time}']   = $row['update_time'];

            return $dataArray;
        }
	}

?>