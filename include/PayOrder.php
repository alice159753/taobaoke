<?php

    include_once("Table.php");

    class PayOrder extends Table
    {
        function PayOrder($myMySQL, $table = "pay_order")
        {
            $this->myMySQL = $myMySQL;
            $this->table = DB_PRE.$table;
        }

        function getIsThroughMap()
        {
            return  array('Y' => '通过', 'N' => '不通过');
        }

        function getData($row)
        {
            $isThroughMap = $this->getIsThroughMap();

            $dataArray = array();
            $dataArray['{no}']               = $row['no'];
            $dataArray['{user_no}']          = $row['user_no'];
            $dataArray['{title}']            = $row['title'];
            $dataArray['{integral}']         = $row['integral'];
            $dataArray['{add_time}']         = $row['add_time'];
            $dataArray['{update_time}']      = $row['update_time'];
            $dataArray['{order_no}']         = $row['order_no'];
            $dataArray['{is_through}']       = $row['is_through'];
            $dataArray['{is_through_title}'] = $isThroughMap[ $row['is_through'] ];

            return $dataArray;
        }


    }

?>