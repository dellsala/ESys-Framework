<?php


/**
 * @package ESys
 */
class ESys_DB_Reflector {


    private $connection;


    /**
     *
     */
    public function __construct (ESys_DB_Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * @param string $tableName
     * @return array|ESys_DB_Reflector_Table A ESys_DB_Reflector_Table object, 
     * or an array of ESys_DB_Reflector_Table objects.
     */
    public function fetchTables ($tableName = null)
    {
        $result = $this->connection->query('SHOW TABLE STATUS');
        if (! $result) {
            $error = $this->connection->describeError(true);
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.$error, E_USER_WARNING);
            return false;
        }
        $tableList = array();
        while ($tableData = $result->fetch()) {
            if (isset($tableName) && $tableName == $tableData['Name']) {
                $table = new ESys_DB_Reflector_Table($tableData, $this->connection);
                $result->free();
                return $table;
            }
            $tableList[] = new ESys_DB_Reflector_Table($tableData, $this->connection);
        }
        $result->free();
        if (isset($tableName)) {
            return false;
        }
        return $tableList;
    }

}



/**
 * @package ESys
 */
class ESys_DB_Reflector_Table {


    private $data;


    private $connection;


    /**
     * @param array $tableData
     * @param ESys_DB_Connection $connection
     */
    public function __construct ($tableData, $connection)
    {
        $this->data = $tableData;
        $this->connection = $connection;
    }


    /**
     * @return string
     */
    public function getName ()
    {
        return $this->data['Name'];
    }


    /**
     * @return array|false An array of ESys_DB_Reflector_Column objects.
     */
    function fetchColumns ()
    {
        $result = $this->connection->query('DESCRIBE `'.$this->getName().'`');
        if (! $result) {
            $error = $this->connection->describeError(true);
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.$error, E_USER_WARNING);
            return false;
        }
        $columnList = array();
        while ($columnData = $result->fetch()) {
            $columnList[] = new ESys_DB_Reflector_Column($columnData, $this->connection);
        }
        $result->free();
        return $columnList;
    }



}


/**
 * @package ESys
 */
class ESys_DB_Reflector_Column {


    private $data;


    private $connection;


    /**
     * @param array $columnData
     * @param ESys_DB_Connection $connection
     */
    public function __construct ($columnData, $connection)
    {
        $this->data = $columnData;
        $this->connection = $connection;
    }


    /**
     * @return string
     */
    public function getName ()
    {
        return $this->data['Field'];
    }


    /**
     * @return string
     */
    public function getType ()
    {
        $type = preg_replace('/\(.*$/', '', $this->data['Type']);
        return strtoupper($type);
    }


    /**
     * @return array An associative array of column type information.
     */
    public function getTypeInfo ()
    {
        $typeInfo = array();
        $typeParts = explode(' ', $this->data['Type']);
        $typeInfo['unsigned'] = in_array('unsigned', $typeParts);
        $typeInfo['zerofill'] = in_array('zerofill', $typeParts);
        $typeInfo['binary'] = in_array('binary', $typeParts);
        preg_match('/^(\w*)(\((.*?)\))?$/', $typeParts[0], $matches);
        $typeInfo['name'] = strtoupper($matches[1]);
        $typeInfo['spec'] = isset($matches[3]) ? $matches[3] : null;
        return $typeInfo;
    }


    /**
     * @return boolean
     */
    public function isNull ()
    {
        return ($this->data['Null'] == 'YES');
    }


    /**
     * @return string
     */
    public function getDefault ()
    {
        return $this->data['Default'];
    }


    /**
     * @return string
     */
    public function getKey ()
    {
        return $this->data['Key'];
    }


}
