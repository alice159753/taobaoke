<?php

    include_once(dirname(__FILE__)."/config.php");
	
	ob_start();
    include_once(INCLUDE_DIR."/Admin.php");
    include_once(INCLUDE_DIR."/JavaScript.php");
    ob_clean();
    
	$done = isset($_REQUEST['done']) ? $_REQUEST['done'] : "";
	$account = isset($_REQUEST['account']) ? $_REQUEST['account'] : "";
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";
    $remember = isset($_REQUEST['remember']) ? $_REQUEST['remember'] : "";

	if( trim($account) == "" || trim($password) == "" )
	{
		JavaScript::alertAndRedirect("account or password can not be empty!", "login.php?r=".time());
        exit;
	}
	
	$myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $myAdmin = new Admin($myMySQL);
    
    $row = $myAdmin->getRow("*", "account = '$account'");

    if ( md5($password) != $row["password"] )
    {
    	JavaScript::alertAndRedirect("account or password not correct!", "login.php?done=$done&r=".time());
        exit;
    }

    $_SESSION['admin_no'] = $row["no"];
    $_SESSION['account']  = $row["account"];

    if( !empty($remember) )
    {
        setcookie(session_name(),session_id(),time()+ 1 * 24 * 3600);
    }

    if ( $done == "" )
    {
        header("location: index.php");
    }
    else
    {
        header("location: $done");
    }

?>
