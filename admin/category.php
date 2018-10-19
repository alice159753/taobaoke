<?php

    include_once("config.php");

    ob_start();
    include_once(INCLUDE_DIR. "/JavaScript.php");
    include_once(INCLUDE_DIR. "/Category.php");
    include_once(INCLUDE_DIR. "/Product.php");
    ob_clean();

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   
    $myCategory = new Category($myMySQL);
    $myProduct = new Product($myMySQL);

    $myTemplate = new Template(TEMPLATE_DIR ."/category.html");

    include_once("common.inc.php");

    //一级分类
    $rows = $myCategory->getRows("*", "parent_no = 0 ORDER BY sort ASC");

    for($i = 0; isset($rows[$i]); $i++)
    {   
        $dataArray = $myCategory->getData($rows[$i]);

        $myTemplate->setReplace("parent_no_lists", $dataArray);
    }

    //获得所有的一级分类，然后在获取一级分类下面所有的二级分类
    $category1 = $rows;

    $lists = array();
    for($i = 0; isset($category1[$i]); $i++)
    {
        $lists[] = $category1[$i];

        $category2 = $myCategory->getRows("*", "parent_no = ".$category1[$i]['no']." ORDER BY sort ASC");

        foreach ($category2 as $key => $value) 
        {
            $lists[] = $value;
        }
    }

    for($i = 0; isset($lists[$i]); $i++)
    {   
        $dataArray = $myCategory->getData($lists[$i]);

        $dataArray['{product_num}'] = 0;

        //获得商品数量
        if( $lists[$i]['parent_no'] == 0 )
        {   
            $dataArray['{product_num}'] = $myProduct->getCount("category_no1 = ". $lists[$i]['no']);
        }
        else
        {   
            $dataArray['{product_num}'] = $myProduct->getCount("category_no2 = ". $lists[$i]['no']);
        }

        $myTemplate->setReplace("list", $dataArray);
    }

    $myTemplate->process();
    $myTemplate->output();

?>