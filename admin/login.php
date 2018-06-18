<?php
	
    include_once(dirname(__FILE__)."/config.php");
    
	$done = isset($_REQUEST['done']) ? $_REQUEST['done'] : "";

    $myTemplate = new Template(TEMPLATE_DIR ."/login.html");

    $dataArray = array();
    $dataArray["{done}"] = $done;
    
    $myTemplate->setReplace("HIDDEN", $dataArray);

    $myTemplate->process();
    $myTemplate->output();
?>
