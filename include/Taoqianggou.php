<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class Taoqianggou extends Table
	{
	    function Taoqianggou($myMySQL, $table = "taoqianggou")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getData($row)
        {
            $dataArray = array();
            $dataArray['{no}']             = $row['no'];
            $dataArray['{category_name}']  = $row['category_name'];
            $dataArray['{click_url}']      = $row['click_url'];
            $dataArray['{end_time}']       = date('Y.m.d', strtotime($row['end_time']));
            $dataArray['{num_iid}']        = $row['num_iid'];
            $dataArray['{pic_url}']        = $row['pic_url'];
            $dataArray['{reserve_price}']  = str_replace(".00", "", $row['reserve_price']);
            $dataArray['{sold_num}']       = $row['sold_num'];
            $dataArray['{start_time}']     = date('Y.m.d', strtotime($row['start_time']));
            $dataArray['{title}']          = $row['title'];
            $dataArray['{total_amount}']   = $row['total_amount'];
            $dataArray['{zk_final_price}'] = $row['zk_final_price'];

            $dataArray['{save_price}'] = $row['reserve_price'] - $row['zk_final_price'];

            return $dataArray;
        }

	}

?>