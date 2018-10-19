<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class Category extends Table
	{
	    function Category($myMySQL, $table = "category")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getIsShow()
        {
            return array('Y' => '是', 'N' => '否');
        }

        function getData($row)
        {
            $isShowMap = $this->getIsShow();

            $dataArray = array();
            $dataArray['{no}']            = $row['no'];
            $dataArray['{title}']         = $row['title'];
            $dataArray['{pic}']           = $row['pic'];
            $dataArray['{add_time}']      = $row['add_time'];
            $dataArray['{update_time}']   = $row['update_time'];
            $dataArray['{is_show}']       = $row['is_show'];
            $dataArray['{is_show_title}'] = $isShowMap[ $row['is_show'] ];
            $dataArray['{parent_title}']  = "";
            $dataArray['{parent_no}']     = $row['parent_no'];
            $dataArray['{sort}']          = $row['sort'];

            $dataArray['{parent_style}'] = 'parent_style';
            if( !empty($row['parent_no']) )
            {
                $dataArray['{parent_title}'] = $this->getValue("title", "no = ". $row['parent_no']);
                $dataArray['{category_style}'] = 'children_style';
            }

            $dataArray['{show_checkbox}'] = $row['is_show'] == 'Y' ? 'checked' : '';

            return $dataArray;
        }

        function getDataClean($row)
        {
            $isShowMap = $this->getIsShow();

            $dataArray = array();
            $dataArray['no']            = $row['no'];
            $dataArray['title']         = $row['title'];
            $dataArray['pic']           = FILE_URL.$row['pic'];
            $dataArray['add_time']      = $row['add_time'];
            $dataArray['update_time']   = $row['update_time'];
            $dataArray['is_show']       = $row['is_show'];
            $dataArray['is_show_title'] = $isShowMap[ $row['is_show'] ];
            $dataArray['sort']          = $row['sort'];

            $dataArray['parent_no']     = $row['parent_no'];

            if( !empty($row['parent_no']) )
            {
                $dataArray['parent_title'] = $this->getValue("title", "no = ". $row['parent_no']);
            }

            $dataArray['show_checkbox'] = $row['is_show'] == 'Y' ? 'checked' : '';

            return $dataArray;
        }
	}

?>