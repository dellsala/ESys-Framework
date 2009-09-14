<?php

/**
 * @package ESys
 */
abstract class ESys_Data_Store_Static implements ESys_Data_Store {


    /**
     * @return array
     */
    abstract protected function getStaticData ();


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
     * @param string
     * @return ESys_Data_Record
     */
    public function fetch ($id)
    {
        $data = $this->getStaticData();
        if (! isset($data[$id])) {
            return null;
        }
        $record = $data[$id];
        $record['id'] = $id;
        return $this->buildRecord($record);
    }


    /**
     * @return ESys_Data_Record
     */
    public function fetchNew ()
    {
        trigger_error(__CLASS__.'::'.__FUNCTION__.
            '(): not supported for this data store type.', E_USER_WARNING);
        return null;
    }


    /**
     * @return array
     */
    public function fetchAll ()
    {
        $idList = array_keys($this->getStaticData());
        $recordList = array();
        foreach ($idList as $id) {
            $recordList[] = $this->fetch($id);
        }
        return $recordList;
    }


    /**
     * @return int
     */
    public function countAll ()
    {
        return count($this->getStaticData());
    }


    /**
     * @param ESys_Data_Record
     * @return void
     */
    public function save (ESys_Data_Record $record)
    {
        trigger_error(__CLASS__.'::'.__FUNCTION__.
            '(): not supported for this data store type.', E_USER_WARNING);
        return false;
    }


    /**
     * @param ESys_Data_Record
     * @return void
     */
    public function delete (ESys_Data_Record $record)
    {
        trigger_error(__CLASS__.'::'.__FUNCTION__.
            '(): not supported for this data store type.', E_USER_WARNING);
        return false;
    }


}
