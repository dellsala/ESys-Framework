<?php

require_once 'ESys/DB/SqlBuilder.php';


/**
 * @package ESys
 */
class ESys_DB_Connection {

    const EVENT_CONNECTION = 'connection';
    const EVENT_ERROR = 'error';
    const EVENT_QUERY = 'query';
    const EVENT_CLOSE = 'close';

    protected $dbhost;
    protected $dbuser;
    protected $dbpass;
    protected $dbase;
    protected $sqlQuery;
    protected $link;
    protected $queryCount;
    protected $listenerList = array();


    /**
     * @param string $user
     * @param string $pass
     * @param string $database
     * @param string $host
     */
    public function __construct ($user, $pass, $database, $host = 'localhost')
    {
        $this->dbuser = $user;
        $this->dbpass = $pass;
        $this->dbase = $database;
        $this->dbhost = $host;
        $this->link = null;
        $this->queryCount = 0;
    }


    /**
     * @return boolean
     */
    public function connect ()
    {
        if ($this->link) {
            return true;
        }
        $startTime = microtime(true);
        $this->link = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass);
        $elapsedTime = round(microtime(true) - $startTime, 4);
        if (! $this->link) {
            $this->notifyListeners(self::EVENT_ERROR, array(
                'code' => mysqli_errno(),
                'message' => mysqli_error(),
            ));
            trigger_error('ESys_DB_Connection: database connection failed', E_USER_WARNING);
            return false; 
        }
        if (! mysqli_select_db($this->link, $this->dbase)) {
            $this->notifyListeners(self::EVENT_ERROR, array(
                'code' => mysqli_errno($this->link),
                'message' => mysqli_error($this->link),
            ));
            trigger_error('ESys_DB_Connection: database selection failed', E_USER_WARNING);
            return false;
        }
        $this->notifyListeners(self::EVENT_CONNECTION, array(
            'message' => "{$this->dbuser}@{$this->dbhost}:{$this->dbase} connected",
            'time' => $elapsedTime,
        ));
        mysqli_set_charset($this->link, 'utf8');
        return true;
    }


    /**
     * @param object $listener An object that supports a notify($event) method.
     * @return void
     */
    public function addListener ($listener)
    {
        if (! is_callable(array($listener, 'notify'))) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): listener does not '.
                'implement a notify() method.', E_USER_ERROR);
            return;
        }
        $this->listenerList[] = $listener;
    }


    protected function notifyListeners($eventType, $data)
    {
        $notification = $data;
        $notification['type'] = $eventType;
        foreach ($this->listenerList as $listener) {
            $listener->notify($notification);
        }
    }
    

    /**
     * @param boolean $textMode
     * @return string
     */
    public function describeError ($textMode = false)
    {
        $errorMsg = $this->link ? mysqli_error($this->link) : mysqli_error();
        $errorNo = $this->link ? mysqli_errno($this->link) : mysqli_errno();
        $sqlQuery = $this->sqlQuery;
        $errorDate = date("F j, Y, g:i a");
        $errorString = array();
        $errorString[] = '-- SQL Error --';
        $errorString[] = "Date: {$errorDate}";
        $errorString[] = "Error Code: {$errorNo}";
        $errorString[] = "Error Message: {$errorMsg}";
        $errorString[] = "Original Query: {$sqlQuery}";
        if (! $textMode) {
            $errorString[0] = '<b>'.$errorString[0].'</b>';
            array_unshift($errorString, '<pre>');
            $errorString[] = '</pre>';
        }
        $errorString = implode("\n", $errorString);
        return $errorString;
    }



    /**
     * @return void
     */
    public function close ()
    {
        mysqli_close($this->link);
        $this->notifyListeners(self::EVENT_CLOSE, array(
            'message' => 'Connection closed.',
        ));
    }



    /**
     * Executes a query
     *
     * For SELECT statements returns a ESys_DB_Connection_Result object.
     * Returns true for any successful non-SELECT query.
     * Returns false on any failed query.
     *
     * @param string $query
     * @return ESys_DB_Connection_Result|boolean
     */
    function query ($query)
    {
        $this->connect();
        $this->sqlQuery = trim($query);
        if (! $this->link) {
            $this->notifyListeners(self::EVENT_ERROR, array(
                'code' => 'NULL',
                'message' => 'Query failed. Not connected.'
            ));
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): query failed, not connected.', E_USER_WARNING);
            return false;
        }
        $startTime = microtime(true);
        $sqlResult = mysqli_query($this->link, $this->sqlQuery);
        $elapsedTime = round(microtime(true) - $startTime, 4);
        $this->notifyListeners(self::EVENT_QUERY, array(
            'query' => $query,
            'time' => $elapsedTime,
        ));
        if ($sqlResult === false) {
            $this->notifyListeners(self::EVENT_ERROR, array(
                'code' => mysqli_errno($this->link),
                'message' => mysqli_error($this->link),
            ));
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): query failed --\n".
                $this->describeError(true), E_USER_WARNING);
            return false;
        }
        $this->queryCount++;
        if (is_bool($sqlResult)) {
            return $sqlResult;
        } else { 
            $result = new ESys_DB_Connection_Result($sqlResult, $this);
            return $result;
        }
    }


    /**
     * @param string $query
     * @return array|false
     */
    public function queryAndFetchRow ($query)
    {
        $result = $this->query($query);
        if (! $result) {
            return false;
        }
        $row = $result->fetch();
        $result->free();
        return $row;
    }


    /**
     * @param string $query
     * @return array|false
     */
    public function queryAndFetchAll ($query)
    {
        $result = $this->query($query);
        if (! $result) {
            return false;
        }
        $allRows = array();
        while ($row = $result->fetch()) {
            $allRows[] = $row;
        }
        $result->free();
        return $allRows;
    }


    /**
     * @return int
     */
    public function countAffectedRows ()
    {
        $affectedRowCount = mysqli_affected_rows($this->link);
        return $affectedRowCount;
    }        
    

    /**
     * @return int
     */
  	public function getInsertId ()
	{
        return mysqli_insert_id($this->link);
	}
	

    /**
     * @return int
     */
    public function countQueries ()
    {
        return $this->queryCount;
    }


    /**
     * @param string $value
     * @return string
     */
    public function escape ($value)
    {
        $this->connect();
        return mysqli_real_escape_string($this->link, $value);
    }


    /**
     * @return ESys_DB_SqlBuilder
     */
    public function getSqlBuilder ()
    {
        return new ESys_DB_SqlBuilder($this);
    }

}


/**
 * @package ESys
 */
class ESys_DB_Connection_Result {


    protected $result;
    protected $link;


    /**
     * @param resource $result
     * @param resource $link
     */
    public function __construct ($result, $link)
    {
        $this->result = $result;
        $this->link = $link;
    }


    /**
     * @return int|false
     */
    public function count ()
    {
        $rowCount = mysqli_num_rows($this->result);
        if ($rowCount === false) { 
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): could not count rows', E_USER_WARNING);
            return false;
        }
        return $rowCount;
    }


    /**
     * @return array|false
     */
    public function fetch ()
    {
        $row = mysqli_fetch_assoc($this->result);
        if (! is_array($row)) { return false; }
        return $row;
    }


    /**
     * @return void
     */
    public function free ()
    {
        mysqli_free_result($this->result);
    }


}

