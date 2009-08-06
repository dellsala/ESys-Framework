<?php

require_once 'ESys/Data/Store.php';


/**
 * @package ESys
 */
abstract class ESys_Data_Store_Sql implements ESys_Data_Store {


    protected $db;


    public function __construct ()
    {
        $this->db = ESys_Application::get('databaseConnection');
    }


    /**
     * @return string
     */
    abstract protected function getTableName ();


    /**
     * @param array
     * @return ESys_Data_Record 
     */
    protected function buildRecord ($data)
    {
        $dataRecordClassName = str_replace('_DataStore', '', get_class($this));
        return new $dataRecordClassName($data);
    }


    /**
     * @param string $query
     * @return array
     */
    protected function queryAndFetchRecords ($query)
    {
		$result = $this->db->query($query);
		if ($result === false) {
		    trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
		        "query failed", E_USER_WARNING);
		    return array();
		}
		if (! is_object($result)) {
		    trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
		        'non-select query specified', E_USER_WARNING);
		    return array();
        }
        $recordList = array();
        while ($row = $result->fetch()) {
            $recordList[] = $this->buildRecord($row);
        }
        $result->free();
		return $recordList;
    }


    /**
     * @param string $id
     * @return ESys_Data_Record|null
     */
    public function fetch ($id)
    {
        $id = (int) $id;
        $table = $this->getTableName();
        $query = "SELECT * FROM `{$table}` WHERE `id` = {$id}";
        $row = $this->db->queryAndFetchRow($query);
        $record = $row ? $this->buildRecord($row) : null;
        return $record;
    }


    /**
     * @return ESys_Data_Record
     */
    public function fetchNew ($data = array())
    {
        unset($data['id']);
        return $this->buildRecord($data);
    }


    /**
     * @return array An array of ESys_Data_Record objects.
     */
    public function fetchAll ()
    {
        $table = $this->getTableName();
        $query = "SELECT * FROM `{$table}`";
        $recordList = $this->queryAndFetchRecords($query);
        return $recordList;
    }


    /**
     * @return int
     */
    public function countAll ()
    {
        $query = "SELECT COUNT(*) AS `count` FROM `{$this->getTableName()}`";
        $row = $this->db->queryAndFetchRow($query);
        return $row['count'];
    }


    /**
     * @param ESys_Data_Record $record
     * @return boolean
     */
    public function save (ESys_Data_Record $record)
    {
        $row = $record->getAll();
        $sqlBuilder = $this->db->getSqlBuilder();
        if ($record->isNew()) {
            $row['id'] = null;
            $query = $sqlBuilder->buildInsert($this->getTableName(), $row);
        } else {
            $query = $sqlBuilder->buildUpdate($this->getTableName(), $row);
        }
        if (! $this->db->query($query)) {
            return false;
        }
        if ($record->isNew()) {
            $record->set('id', $this->db->getInsertId());
        }
        return true;
    }


    /**
     * @param ESys_Data_Record $record
     * @return boolean
     */
    public function delete (ESys_Data_Record $record)
    {
        $id = (int) $record->getId();
        $table = $this->getTableName();
        $query = "DELETE FROM `{$table}` WHERE id = {$id}";
        return $this->db->query($query);
    }


}