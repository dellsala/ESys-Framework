<?php

require_once 'ESys/DB/Connection.php';



class ESys_TestUtility_DbConnectionStub extends ESys_DB_Connection {


    private $registeredQueries = array();


    public function __construct ()
    {
    }


    public function registerQuery ($query, $resultData)
    {
        $this->registeredQueries[$query] = $resultData;
    }


    public function reset ()
    {
        $this->registeredQueries = array();
    }


    public function query ($query)
    {
        if (! isset($this->registeredQueries[$query])) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): query '{$query}' not registered.",
                E_USER_WARNING);
            return false;
        }
        if (is_bool($this->registeredQueries[$query])) {
            return $this->registeredQueries[$query];
        }
        return new ESys_TestUtility_DbConnectionStub_Result($this->registeredQueries[$query]);
    }


}


class ESys_TestUtility_DbConnectionStub_Result  extends ESys_DB_Connection_Result {


    private $data;


    public function __construct ($data)
    {
        $this->data = $data;
        reset($this->data);
    }


    public function count ()
    {
        return count($this->data);
    }


    public function fetch ()
    {
        $row = current($this->data);
        next($this->data);
        return $row;
    }


    public function free ()
    {
        $this->data = null;
    }


}
