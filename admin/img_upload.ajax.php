<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    ob_clean();

    $oneArray = FileTools::uploadOne($_FILES, 'file');

    if( $oneArray['status'] == 'error' )
    {
        Output::error($oneArray['message'],array(), 1);
    }

    Output::succ('succ', array('url' => $oneArray['filename']));

?>