<?php


/**
 * @package ESys
 */
class ESys_Factory {


    protected $instanceList;


    /**
     * @param string
     * @return mixed
     */
    public function  getInstance ($id)
    {
        if (! isset($this->instanceList[$id])) {
            if (! $className = $this->mapIdToClassName($id)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__.
                    "(): unrecognized instance id {$id}", E_USER_WARNING);
                return null;
            }
            $this->instanceList[$id] = $this->createInstance($className);
        }
        return $this->instanceList[$id];
    }


    /**
     * @param string
     * @return string
     */
    protected function mapIdToClassName ($id)
    {
        return $id;
    }


    /**
     * @param string
     * @return mixed
     */
    protected function createInstance ($className)
    {
        if (! class_exists($className)) {
            $classFile = str_replace('_', '/', $className).'.php';
            require_once $classFile;
        }
        return new $className();
    }


}