<?php

	include_once("Table.php");

	class UserProduct extends Table
	{
	    function UserProduct($myMySQL, $table = "user_product")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }
	}

?>