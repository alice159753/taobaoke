<?php

	include_once("Table.php");

    ob_start();
    include_once(INCLUDE_DIR. "/Role.php");
    ob_clean();

	class Article extends Table
	{
	    function Article($myMySQL, $table = "article")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getArticleTypeMap()
        {
            return  array(0 => '通用', 1 => '首页');
        }

        function getData($row)
        {
            $dataArray = array();
            $dataArray["{no}"]          = $row['no'];
            $dataArray["{title}"]       = $row['title'];
            $dataArray["{author}"]      = $row['author'];
            $dataArray['{content}']     = $row['content'];
            $dataArray["{view_count}"]  = $row['view_count'];
            $dataArray["{pubdate}"]     = $row['pubdate'];
            $dataArray["{is_through}"]  = $row['is_through'] == 'Y' ? '通过' : '未通过';
            $dataArray["{top}"]         = $row['top'];
            $dataArray["{thumb_pic}"]   = $row['thumb_pic'];
            $dataArray["{add_time}"]    = $row['add_time'];
            $dataArray["{description}"] = $row['description'];
            $dataArray["{pic_lists}"]   = $row['pic_lists'];

            $dataArray["{url}"] = URL."/subject_detail.php?no=".$row['no'];

            return $dataArray;
        }
	}

?>