<?php

	include_once("Table.php");

	class Role extends Table
	{
	    function Role($myMySQL, $table = "role")
	    {
	        $this->myMySQL = $myMySQL;
            $this->table = DB_PRE.$table;
	    }
	}

?>