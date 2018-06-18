<?php

    include_once("Table.php");

    class OpenUser extends Table
    {
        function OpenUser($myMySQL, $table = "open_user")
        {
            $this->myMySQL = $myMySQL;
            $this->table = DB_PRE.$table;
        }

        function getLoginTypeMap()
        {
            return  array('weixin' => '微信', 'qq' => 'qq','xiaochengxu'=>'小程序');
        }

        function getSexMap()
        {
            return  array(0 => '未知', 1 => '男', 2=>'女');
        }
    }

?>