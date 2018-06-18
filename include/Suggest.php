<?php

	include_once("Table.php");

	class Suggest extends Table
	{
	    function Suggest($myMySQL, $table = "suggest")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }
	}

?>