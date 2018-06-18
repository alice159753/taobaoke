<?php

    include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

    class ProjectTableDetail extends Table
    {
        function ProjectTableDetail($myMySQL, $table = "project_table_detail")
        {
            $this->myMySQL = $myMySQL;
            $this->table = DB_PRE.$table;
        }

        function getData($row)
        {
            $dataArray = array();
            $dataArray['{no}']          = $row['no'];
            $dataArray['{name}']        = $row['name'];
            $dataArray['{type}']        = $row['type'];
            $dataArray['{display}']     = $row['display'];
            $dataArray['{html_type}']   = $row['html_type'];
            $dataArray['{show_type}']   = $row['show_type'];
            $dataArray['{is_order}']    = $row['is_order'];
            $dataArray['{search_type}'] = $row['search_type'];
            $dataArray['{add_time}']    = $row['add_time'];
            $dataArray['{update_time}'] = $row['update_time'];

            return $dataArray;
        }
    }

?>