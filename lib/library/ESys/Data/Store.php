<?php

/**
 * @package ESys
 */
interface ESys_Data_Store {


    /**
     * @param string $id
     * @return ESys_Data_Record|null
     */
    public function fetch ($id);


    /**
     * @return ESys_Data_Record
     */
    public function fetchNew ();


    /**
     * @return array
     */
    public function fetchAll ();


    /**
     * @return int
     */
    public function countAll ();


    /**
     * @param ESys_Data_Record $record
     * @return boolean
     */
    public function save (ESys_Data_Record $record);


    /**
     * @param ESys_Data_Record $record
     * @return boolean
     */
    public function delete (ESys_Data_Record $record);


}
