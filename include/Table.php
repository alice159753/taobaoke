<?php

class Table
{
    var $myMySQL;
    var $table;
    var $errorMessage;
    var $errorCode;
    
    function Table($myMySQL, $table)
    {
        $this->myMySQL = $myMySQL;
        $this->table = $table;
    }
    
    function add($dataArray)
    {
        $result = $this->myMySQL->addRow($this->table, $dataArray);

        if ( $result < 0 )
        {
            $this->errorMessage = "Table::add() ". $this->myMySQL->getErrorMessage();
            return $result;
        }
        
        return $result;
    }

    function addRow($dataArray)
    {
        return $this->add($dataArray);
    }
    
    function get($columns, $condition)
    {
        return $this->getRow($columns, $condition);
    }
    
    function getRow($columns, $condition = null)
    {
        if ( $condition == null )
        {
            $condition = $columns;
            $columns = "*";
        }
                
        $row = $this->myMySQL->getRow($this->table, $columns, $condition);
        
        return $row;
    }
    
    function getValue($column, $condition)
    {
        $row = $this->myMySQL->getRow($this->table, $column, $condition);
        
        return $row[$column];
    }
    
    function getList($columns, $condition)
    {
        return $this->getRows($columns, $condition);
    }

    function getRows($columns, $condition = null)
    {
        if ( $condition == null )
        {
            $condition = $columns;
            $columns = "*";
        }
        
        $rows = $this->myMySQL->getRows($this->table, $columns, $condition);
        
        return $rows;
    }

    function update($dataArray, $condition)
    {
        $result = $this->myMySQL->updateRows($this->table, $dataArray, $condition);
        
        return $result;
    }
    
    function remove($condition)
    {
        $result = $this->myMySQL->deleteRows($this->table, $condition);

        return $result;
    }

    function delete($condition)
    {
        return $this->remove($condition);
    }
    
    function getLastSQL()
    {
        return $this->myMySQL->getLastSQL();
    }
    
    function getCount($condition = "1=1")
    {
        $row = $this->myMySQL->getRow($this->table, "count(*) AS cnt", $condition);

        return $row["cnt"];
    }
    
    function getPage($columns, $page, $pageSize, $condition = "1=1")
    {
        $realCount = $this->getCount($condition);

        $totalPage = floor($realCount/$pageSize) + ( $realCount%$pageSize ? 1:0 );
    
        if ( $totalPage == 0 )
        {
            $totalPage = 1;
        }

        if ( $totalPage < $page )
        {
            $page = $totalPage;
        }
    
        $startNo = ($page - 1) * $pageSize;
        $endNo = $page * $pageSize - 1;
    
        $rows = $this->myMySQL->getRows($this->table, $columns, $condition ." LIMIT $startNo, $pageSize");
        
        return $rows;
    }

    function getSpecialCount($FieldMapping, $paramArray)
    {
        $condition = "select count(*) as cnt from ";

        $condition .= self::getSpecialCondition($FieldMapping, $paramArray);

        if( $this->myMySQL->executeQuery($condition) < 0 )
        {
            return 0;
        }

        $row = $this->myMySQL->fetchArray(-1, "");

        return $row["cnt"];
    }

    function getSpecialCondition($FieldMapping, $paramArray = array())
    {
        $titleList = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
                           "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
                           "U", "V", "W", "X", "Y", "Z");

        $query = "(select record_no, ";
        $index = 0;
        $fieldMap = array();
        foreach ($FieldMapping as $no => $itemArray) 
        {
            $field_type = $FieldMapping[ $no ]['field_type'];

            $query .= "max(IF(parent_no = $no,`".$field_type."`,'')) as '".$titleList[$index]."', ";

            $fieldMap["$no"] = $titleList[$index];

            $index++;
        }
        
        $query = trim($query);
        $query = substr($query, 0, -1);

        $query .= " from $this->table group by record_no) as new_table";

        $condition .= $query;

        $queryList = array();
        foreach ($paramArray as $no_field_type => $value) 
        {
            list($no, $field_type) = explode("_", $no_field_type);

            if( empty($value) || empty($fieldMap[$no]) )
            {
                continue;
            }  

            $queryList[] = "".$fieldMap[$no]." like '%".$value."%'";
        }

        $query = implode(" AND ", $queryList);

        $condition .= empty($query) ? "" : " where $query";

        return $condition;
    }

    function getSpecialPage($page, $pageSize, $realCount, $FieldMapping, $paramArray)
    {
        $condition = "select record_no from ";
        $condition .= self::getSpecialCondition($FieldMapping, $paramArray);

        $totalPage = floor($realCount/$pageSize) + ( $realCount%$pageSize ? 1:0 );
    
        if ( $totalPage == 0 )
        {
            $totalPage = 1;
        }

        if ( $totalPage < $page )
        {
            $page = $totalPage;
        }
    
        $startNo = ($page - 1) * $pageSize;
        $endNo = $page * $pageSize - 1;

        $condition .= " LIMIT $startNo, $pageSize";

        if( $this->myMySQL->executeQuery($condition) < 0 )
        {
            return NULL;
        }

        $rows = $this->myMySQL->fetchArrays(-1, "");
        
        return $rows;
    }
    
    
    function getPageCount($pageSize, $condition = "1=1")
    {
        $totalCount = $this->getCount($condition);

        $pageCount = floor($totalCount/$pageSize) + ( $totalCount%$pageSize ? 1:0 );
        
        return $pageCount;
    }

    function getMappingByNo($column, $condition = "1=1")
    {
        $rows = $this->myMySQL->getRows($this->table, "no, ". $column, $condition);

        $result = array();
        
        for($i = 0; isset($rows[$i]); $i++)
        {
            $result[$rows[$i]["no"]] = $rows[$i][$column];
        }
        
        return $result;
    }

    function getMapping($key, $columns, $condition = "1=1")
    {
        if ( $columns == "*" )
        {
            $rows = $this->myMySQL->getRows($this->table, $columns, $condition);
        }
        else
        {
            $rows = $this->myMySQL->getRows($this->table, $key .", ". $columns, $condition);
        }
        
        $result = array();
        
        for($i = 0; isset($rows[$i]); $i++)
        {
            $result[$rows[$i][$key]] = $rows[$i];
        }
        
        return $result;
    }

    function getUniqueMapping($key, $column, $condition = "1=1")
    {
        $rows = $this->myMySQL->getRows($this->table, $key .", ". $column, $condition);
        
        $result = array();
        
        for($i = 0; isset($rows[$i]); $i++)
        {
            $result[$rows[$i][$key]] = $rows[$i][$column];
        }
        
        return $result;
    }
    
    function getInsertID()
    {
        return $this->myMySQL->getInsertID();
    }

    function lock($mode)
    {
        $sql = "LOCK TABLES ". $this->table ." ". $mode;
        
        return $this->myMySQL->executeUpdate($sql);
    }

    function unlock()
    {
        $sql = "UNLOCK TABLES";

        return $this->myMySQL->executeUpdate($sql);
    }


}

?>