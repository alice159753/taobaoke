<?php

    include_once(dirname(__FILE__)."/../config.cmd.php");


    ob_start();
    include_once(INCLUDE_DIR. "/TaobaoApi.php");
    ob_clean();

    $myProduct = new Product($myMySQL);
    $myCategory = new Category($myMySQL);
    $myTaobaoApi = new TaobaoApi();



    $lists = $myTaobaoApi->getMaterial();

print_r($lists);



?>