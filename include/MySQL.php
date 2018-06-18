<?php

class MySQL
{
    var $server = "";
    var $user = "";
    var $password = "";
    var $database = "";
    
    var $connection  = 0;
    var $queryID = 0;
    var $record = array();
    var $sql;

    var $errorMessage = "";
    var $errorCode = 0;

    var $usePconnect = 0;
    var $logFlag;
    var $logPath;
    var $debugFlag;
    
    var $auto_reconnect;
    var $fetch_result_type;

    function MySQL()
    {
        $this->connection = 0;
        $this->usePconnect = 0;
        
        $this->logFlag = false;
        $this->logPath = "./";

        $this->debugFlag = false;
        $this->auto_reconnect = false;
        $this->fetch_result_type = MYSQL_ASSOC;
    }

    function createDB($server, $user, $password, $dbname)
    {
        $this->server   = $server;
        $this->user     = $user;
        $this->password = $password;

        $this->connection = mysql_connect($server, $user, $password);

        if (!$this->connection) die('Could not connect: $server, $user, $password, $dbname' . mysql_error());

        $err = mysql_query("CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci", $this->connection);

        if(!$err) die("Error creating database: $server, $user, $password, $dbname" . mysql_error());

        // mysql_close($this->connection);

        return true;
    }

    function createTable($database, $table, $dataArray)
    {
        if (!$this->connection)
        {
            die("MySQL::connect($database) error => ". mysql_error() ." (". mysql_errno() .")\n");
        }

        if( !mysql_select_db($database, $this->connection) )
        {
            die("MySQL::selectDatabase() error => ". mysql_error() ." (". mysql_errno() .")\n");
        }

        $sql  = "CREATE TABLE $table\n(\n";

        foreach ($dataArray["field"] as $field => $attrArray) 
        {
            $type = $attrArray["type"];
            $extra = $attrArray["extra"];
            $null = $attrArray["null"];
            $defualt = $attrArray["defualt"];

            $sql .= " `$field` $type $null $defualt $extra,\n";
        }

        for ($i = 0; isset($dataArray["primary"][$i]); $i++) 
        { 
            $dataArray["primary"][$i] = "`". $dataArray["primary"][$i] ."`";
        }

        $primary = implode(",", $dataArray["primary"]);

        $sql .= " PRIMARY KEY  ($primary),\n";

        for ($i = 0; isset($dataArray["index"][$i]); $i++) 
        { 
            $indexArray = $dataArray["index"][$i];

            for ($j = 0; isset($indexArray[$j]); $j++) 
            { 
                $indexArray[$j] = "`". $indexArray[$j] ."`";
            }

            $index = implode(",", $indexArray);

            $sql .= " KEY `idx_". $table ."_". $i ."` ($index),\n";
        }

        $engine = $dataArray["engine"];
        $charset = $dataArray["charset"];

        $sql  = trim($sql, ",\n");
        $sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;";

        if(!mysql_query($sql, $this->connection))
        {
            die("MySQL::createTable() error => ". mysql_error() ." (". mysql_errno() .")\n");
        }
    }

    function getFetchResultType()
    {
        return $this->fetch_result_type;
    }
    
    function setFetchResultType($fetch_result_type)
    {
        $this->fetch_result_type = $fetch_result_type;
    }
    
    function setAutoReconnect($flag, $waiting_seconds = 5)
    {
        $this->auto_reconnect = $flag;
    }
    
    function reconnect()
    {
        echo "MySQL::reconnect()\n";
        
        if ( $this->usePconnect == 1 )
        {
            mysql_pclose($this->connection);
        }
        else
        {
            mysql_close($this->connection);
        }
        
        unset($this->connection);
        
        sleep(5); 
        
        $this->connect($this->server, $this->user, $this->password, $this->database);
    }

    function connect($server, $user, $password, $database)
    {
        // connect to db server
        // return: true, false
        
        $this->server   = $server;
        $this->user     = $user;
        $this->password = $password;
        $this->database = $database;

        if ( $this->usePconnect == 1 )
        {
            $this->connection = mysql_pconnect($server, $user, $password);
        }
        else
        {
            $this->connection = mysql_connect($server, $user, $password);
        }

        if ( !$this->connection )
        {
            echo "MySQL::connect($server, $user, $password, $database) error => ". mysql_error() ." (". mysql_errno() .")\n";
            
            return false;
        }

        $this->setCharset("UTF8");
        mysql_query("SET time_zone = 'Asia/Shanghai'", $this->connection);

        if ( $database != "" )
        {
            return $this->selectDatabase($database);
        }

        
        return true;
    } 
    
    function setDebug($debugFlag)
    {
        $this->debugFlag = $debugFlag;
    }
    
    function setLog($logFlag, $logPath = "")
    {
        $this->logFlag = $logFlag;
        
        if ( $logPath != "" ) 
        {
            $this->logPath = $logPath;
        }
    }
    
    function log($startTime, $endTime, $sql)
    {
        if ( !$this->logFlag )
        {
            return;
        }
        
        list($usec, $sec) = explode(" ", $startTime);
        $start_time = (float)$usec + (float)$sec;
        
        list($usec, $sec) = explode(" ", $endTime);
        $end_time = (float)$usec + (float)$sec;
        
        $process_time = ($end_time - $start_time);
         
        $message = date("H:i:s") ." ". $process_time ." ". $sql ."\n";
        $filename = $this->logPath ."/mysql_". date("Ymd") .".log";
        $fd = fopen($filename, 'a+');
        
        fwrite($fd, $message, 10240);
        fclose($fd);
    }

    function setCharset($charset)
    {
        return mysql_query("set names ". $charset, $this->connection);
    }

    function getErrorMessage()
    {
        $this->errorMessage = mysql_error();
        
        return $this->errorMessage;
    }

    function getErrorCode()
    {
        $this->errorCode = mysql_errno();
        
        return $this->errorCode;
    }

    function selectDatabase($database)
    {
        $this->database = $database;

        if( !mysql_select_db($this->database, $this->connection) )
        {
            echo "MySQL::selectDatabase() error => ". mysql_error() ." (". mysql_errno() .")\n";
            
            return false; 
        }

        return true;
    }

    function executeUpdate($queryString, $database = "")
    {
        // return: -1  (invalid sql)
        // return: -2 (select database error)
        // return: >= 0 (affected rows)
        if ( $database != "" )
        {
            if ( !$this->selectDatabase($database) )
            {
                return -2;
            }
        }

        $this->sql = $queryString;

        $startTime = microtime();

        $this->queryID = mysql_query($queryString, $this->connection);

        $endTime = microtime();
        
        $this->log($startTime, $endTime, $this->sql);

        if ( !$this->queryID )
        {
            echo "MySQL::executeUpdate() error, SQL = $queryString => ". mysql_error() ." (". mysql_errno() .")\n";
            
            if ( (mysql_errno() == 2006 || mysql_errno() == 2003) && $this->auto_reconnect )
            {
                $this->reconnect();
            }

            return -1; 
        }

        return mysql_affected_rows($this->connection);
    }

    function executeQuery($queryString, $database = "")
    {
        // return: -1 (invalid sql)
        // return: -2 (select database error)
        // return: >= 0 (number of rows)

        if ( $database != "" )
        {
            if ( !$this->selectDatabase($database) )
            {
                return -2;
            }
        }
        
        $this->sql = $queryString;

        $startTime = microtime();

        $this->queryID = mysql_query($queryString, $this->connection);

        $endTime = microtime();

        $this->log($startTime, $endTime, $this->sql);
                
        if ( !$this->queryID )
        {
            echo "MySQL::executeQuery() error, SQL = $queryString => ". mysql_error() ." (". mysql_errno() .")\n";
            
            if ( (mysql_errno() == 2006 || mysql_errno() == 2003) && $this->auto_reconnect )
            {
                $this->reconnect();
            }

            return -1; 
        }

        return mysql_num_rows($this->queryID);
    }

    function fetchAssociativeArray($queryID=-1, $queryString="")
    {
        // retrieve row
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }

        if ( isset($this->queryID) )
        {
            $this->record = mysql_fetch_assoc($this->queryID);
        }
        else
        {
            if ( !empty($queryString) )
            {
                return null; // "Invalid query id"
            }
            else
            {
                return null; // "Invalid query id"
            }
        }

        return $this->record;
    }

    function fetchArray($queryID=-1, $queryString="")
    {
        // retrieve row
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }
        
        if ( $this->queryID == FALSE )
        {
            echo "MySQL::fetchArray() error => ". mysql_error() ." (". mysql_errno() .")\n";
        }

        if ( isset($this->queryID) )
        {
            if ( !($this->record = mysql_fetch_array($this->queryID, $this->fetch_result_type)) )
            {
                if ( $this->debugFlag ) echo $this->sql ."\r\n";
            }
        }
        else
        {
            if ( !empty($queryString) )
            {
                return null; // "Invalid query id"
            }
            else
            {
                return null; // "Invalid query id"
            }
        }

        return $this->record;
    }

    function fetchArrays($queryID=-1, $queryString="")
    {
        $result = array();
        // retrieve row
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }

        if ( !isset($this->queryID) )
        {
            if ( !empty($queryString) )
            {
                return null; // "Invalid query id"
            }
            else
            {
                return null; // "Invalid query id"
            }
        }

        for( $index = 0; ; $index ++ )
        {
            if ( !($record = mysql_fetch_array($this->queryID, $this->fetch_result_type)) )
            {
                if ( $this->debugFlag && $index == 0 ) echo $this->sql ."\r\n";
                
                break;
            }
            
            $result[$index] = $record;
        }

        return $result;
    }

    function free_result($queryID=-1)
    {
        // retrieve row
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }

        return @mysql_free_result($this->queryID);
    }

    function queryFirst($queryString)
    {
        // does a query and returns first row
        $queryID = $this->query($queryString);
        $returnArray = $this->fetch_array($queryID, $queryString);
        $this->free_result($queryID);

        return $returnArray;
    }

    function dataSeek($pos, $queryID=-1)
    {
        // goes to row $pos
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }

        return mysql_data_seek($this->queryID, $pos);
    }

    function numRows($queryID=-1)
    {
        // returns number of rows in query
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }

        return mysql_num_rows($this->queryID);
    }

    function num_fields($queryID=-1)
    {
        // returns number of fields in query
        if ($queryID!=-1)
        {
            $this->queryID=$queryID;
        }

        return mysql_num_fields($this->queryID);
    }

    function ping()
    {
        return mysql_ping($this->connection);
    }

    function close()
    {
        // closes connection to the database

        $result = mysql_close($this->connection);
        unset($this->connection);
        return $result;
    }

    function getInsertID()
    {
        return mysql_insert_id($this->connection);
    }

    function addRow($table, $dataArray)
    {
        $column = $this->toColumnString($dataArray);
        $value = $this->toValueString($dataArray);

        $this->sql = "INSERT INTO ".$this->database.".$table ($column) "." VALUES ($value)";

        $result = $this->executeUpdate($this->sql, $this->database);

        return $result;
    }

    function addOrUpdateRow($table, $dataArray)
    {
        $column = $this->toColumnString($dataArray);
        $value = $this->toValueString($dataArray);
        $column_value = $this->toColumnValueString($dataArray);

        $this->sql = "INSERT INTO ".$this->database.".$table ($column) "." VALUES ($value) ".
                     "ON DUPLICATE KEY UPDATE $column_value";

        $result = $this->executeUpdate($this->sql, $this->database);

        return $result;
    }

    function getRow($table, $column, $condition)
    {
        if ( is_array($column) )
        {
            $columnString = $this->toColumnString($column);
        }
        else
        {
            $columnString = $column;
        }

        if ( is_array($condition) )
        {
            $conditionString = $this->toConditionString($condition);
        }
        else
        {
            $conditionString = $condition;
        }

        $this->sql = "SELECT $columnString ".
                     "FROM ".$this->database.".$table ";

        if ( $conditionString )
        {
            $this->sql .= "WHERE $conditionString ";
        }

        if ( $this->executeQuery($this->sql) < 0 )
        {
            return NULL;
        }

        return $this->fetchArray(-1, "");
    }

    function getRows($table, $column, $condition)
    {
        if ( is_array($column) )
        {
            $columnString = $this->toColumnString($column);
        }
        else
        {
            $columnString = $column;
        }

        if ( is_array($condition) )
        {
            $conditionString = $this->toConditionString($condition);
        }
        else
        {
            $conditionString = $condition;
        }

        $this->sql = "SELECT $columnString ".
                     "FROM ".$this->database.".$table ";

        if ( $conditionString )
        {
            $this->sql .= "WHERE $conditionString ";
        }

        if ( $this->executeQuery($this->sql) < 0 )
        {
            return NULL;
        }

        return $this->fetchArrays(-1, "");
    }

    function updateRows($table, $dataArray, $condition)
    {
        if ( is_array($dataArray) )
        {
            $set = $this->toSetString($dataArray);
        }
        else
        {
            $set = $dataArray;
        }

        if ( is_array($condition) )
        {
            $conditionString = $this->toConditionString($condition);
        }
        else
        {
            $conditionString = $condition;
        }

        $this->sql = "UPDATE ".$this->database.".$table ".
                     "SET $set ";

        if ( $condition )
        {
            $this->sql .= "WHERE $conditionString ";
        }

        $result = $this->executeUpdate($this->sql);

        return $result;
    }

    function deleteRows($table, $condition)
    {
        if ( is_array($condition) )
        {
            $conditionString = $this->toConditionString($condition);
        }
        else
        {
            $conditionString = $condition;
        }

        $this->sql = "DELETE FROM ".$this->database.".$table ";

        if ( $condition )
        {
            $this->sql .= "WHERE $conditionString";
        }

        $result = $this->executeUpdate($this->sql);

        return $result;
    }

    function toConditionString($dataArray)
    {
        if ( $dataArray == NULL )
        {
            return $NULL;
        }

        $condition = "";

        reset($dataArray);

        while( $key = each($dataArray) )
        {
            if ( $condition != "" )
            {
                $condition = $condition ." AND ";
            }

            $condition = $condition . $key['key'] ." = '".$key['value']."' ";
        }

        return $condition;
    }

    function toColumnString($dataArray)
    {
        $result = "";

        reset($dataArray);

        while( $key = each($dataArray) )
        {
            if ( $result != "" )
            {
                $result .= ", ";
            }

            $result .= $key['key'];
        }

        return $result;
    }

    function toValueString($dataArray)
    {
        $result = "";

        reset($dataArray);

        while( $key = each($dataArray) )
        {
            if ( $result != "" )
            {
                $result .= ", ";
            }

            if ( trim(strtolower($key['value'])) == 'sysdate()' ||
                 trim(strtolower($key['value'])) == 'now()' )
            {
                $result .= $this->escapeString($key['value']);
            }
            else
            {
                $result .= "'".$this->escapeString(stripslashes($key['value']))."'";
            }
        }

        return $result;
    }

    function toColumnValueString($dataArray)
    {
        $result = '';

        foreach ($dataArray as $column => $value) 
        {
            if ( trim(strtolower($value)) == 'sysdate()' ||
                 trim(strtolower($value)) == 'now()' )
            {
                $value = $this->escapeString($value);
            }
            else
            {
                $value = "'".$this->escapeString(stripslashes($value))."'";
            }

            $result .= "$column = $value,";
        }

        $result = empty($result) ? '' : substr($result, 0, -1);

        return $result;
    }

    function toSetString($dataArray)
    {
        $result = "";

        reset($dataArray);

        while( $key = each($dataArray) )
        {
            if ( $result != "" )
            {
                $result .= ", ";
            }

            if ( trim(strtolower($key['value'])) == 'sysdate()' ||
                 trim(strtolower($key['value'])) == 'now()' )
            {
                $result .= $key['key'] ." = ". $this->escapeString($key['value']);
            }
            else
            {
                $result .= $key['key'] ." = '". $this->escapeString(stripslashes($key['value'])) ."'";
            }
        }

        return $result;
    }

    function getLastSQL()
    {
        return $this->sql;
    }

    function escapeString($input)
    {
        return mysql_escape_string($input);
    }
}

?>