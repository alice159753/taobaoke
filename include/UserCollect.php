<?php

	include_once("Table.php");

	class UserCollect extends Table
	{
	    function UserCollect($myMySQL, $table = "user_collect")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }
	}

?>