<?php

    include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    class UserLogin extends Table
    {
        function UserLogin($myMySQL, $table = "user_login")
        {
            $this->myMySQL = $myMySQL;
            $this->table = DB_PRE.$table;
        }

        function login($userRow)
        {
            $dataArray = array();
            $dataArray['user_no'] = $userRow['no'];
            $dataArray['add_time'] = 'now()';

            $this->addRow($dataArray);
        }

        function getData($row)
        {
            $dataArray = array();
            $dataArray['{no}'] = $row['no'];
            $dataArray['{user_no}'] = $row['no'];
            $dataArray['{login_time}'] = $row['login_time'];

            return $dataArray;
        }
    }


?>