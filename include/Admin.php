<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class Admin extends Table
	{
	    function Admin($myMySQL, $table = "admin")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }
	}

?>