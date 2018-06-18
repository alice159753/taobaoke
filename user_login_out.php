<?php


    include_once("config.php");


    unset($_SESSION);

    unset($_SESSION['user_no']);


    header('Location:user_login.php');




?>