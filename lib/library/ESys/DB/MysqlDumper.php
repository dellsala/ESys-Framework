<?php

/**
* Database Dump Class.
*
* Backs up a database, creating a file for each day of the week,
* using the mysqldump utility.
* Can compress backup file with gzip of bzip2
* Requires the user executing the script has permission to execute
* mysqldump.
* Adapted from code by Harry Fuecks found in "The PHP Anthology Volume I, Chapter 3"
*
* <code>
* $dumper = new ESys_DB_MysqlDumper('harryf', 'secret', 'sitepoint');
* if (! $dumper->dump('/home/joe/mybackup.tar.gz', 'gz')) {
*     echo $dumper->getErrorCode();
* }
* </code>
*
* @package ESys
*/
class ESys_DB_MysqlDumper {

    private $dbUser;

    private $dbPass;

    private $dbName;

    private $zipTypes = array(
        'gz'=>'gzip',
        'bz2'=>'bzip2',
    );

    private $errorCode = '';

    private $mysqldumpPath = 'mysqldump';

    private $modeFlags = array(
        'full' => '--opt',
        'structure' => '--opt -d',
        'data' => '--opt -t',
    );
 
    private $mode = 'full';

 
    /**
     * @param string $dbUser MySQL User Name
     * @param string $dbPass MySQL User Password
     * @param string $dbName Database to select
     * @access public
     */
    public function __construct ($dbUser, $dbPass, $dbName)
    {
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;
    }
 
    
    /**
     * @param string $fileName
     * @param string $zip Compression format.
     * @param array $tables
     */
    private function _buildCommand ($fileName, $zip, $tables)
    {
        $zip = (string) $zip;
        if (is_array($tables)) {
            $tables = implode(' ', $tables);
        }
        $command = sprintf('%s %s -u %s -p%s %s %s',
            $this->mysqldumpPath,
            $this->modeFlags[$this->mode], 
            $this->dbUser, 
            $this->dbPass, 
            $this->dbName,
            $tables
        );
        if (array_key_exists($zip, $this->zipTypes)) {
            $command .= '| '.$this->zipTypes[$zip].' ';
            //$fileName .= '.'.$zip;    
        }
        $command .= '> '.$fileName;
        return $command;
    }


    /**
     * @param string $fileName Full path, and name of backup file)
     * @param string $zip Zip type; gz - gzip [default], bz2 - bzip
     * @param array|string $tables Array or string of tables names separated by spaces.
     * @return boolean
     */
    public function dump ($fileName, $zip = null, $tables = null)
    {
        exec($this->mysqldumpPath, $out, $error);
        if ($error != 1) {
            $this->errorCode = 'invalid mysqldump binary';
            trigger_error('ESys_DB_MysqlDumper : backup failed -> '.
                "'".$this->mysqldumpPath."' is not an executable ".
                'mysqldump binary. check your path.');
            return false;
        }
        $command = $this->_buildCommand($fileName, $zip, $tables);
        exec($command, $output, $error);
        if ($error) {
            $this->errorCode = $error;
            trigger_error('ESys_DB_MySQLDump::backup(): backup failed -> ' . $error, 
                E_USER_NOTICE);
            return false;
        }
        return true;
    }


    /**
     * @return int
     */
    public function getErrorCode () 
    { 
        return $this->errorCode; 
    } 


    /**
     * @param string $mode
     * @return void
     */
    function setMode ($mode)
    {
        if (! array_key_exists($mode, $this->modeFlags)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                'Invalid mode.', E_USER_ERROR);
            return;
        }
        $this->mode = $mode;
    }


    /**
     * @param string $path
     * @return void
     */
    function setMysqldumpPath ($path) 
    { 
        $this->mysqldumpPath = $path; 
    } 


}
