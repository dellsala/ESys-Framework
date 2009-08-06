<?php
/**
 * @package ESys
 * @author Dell Sala
 */
 

/**
 * Defines wether or not the script is called from the CLI (Command Line Interface).
 */
define('ESYS_ERRORREPORTER_CLI', php_sapi_name() == 'cli' ? true : false);


/**
 * Error Reporting Class
 *
 * This class captures errors as they occur, and adds them to an internal stack.
 * It can produce a text report of all the errors that includes a full stack 
 * trace for each error.
 *
 * It can be configured to report the errors to STOUT as they occur.
 *
 * @package ESys
 */
class ESys_ErrorReporter {

    private $errorStack = array();

    private $realtimeReporting = false;

    private $errorReportingLevel = E_ALL;

    private $listenerList = array();

    private $dieOnError = false;
    
    private $observingPear = false;


    /**
     * @param int $errorLevel
     */
    public function __construct ($errorLevel = null) {
        $this->setErrorReportingLevel($errorLevel);
        $this->observePhpErrors();
    }


    /**
     * @return void
     */
    public function observePearErrors () {
        include_once 'PEAR.php';
        PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($this, 'pushError'));
        $this->observingPear = true;
    }


    /**
     * @param object $listener An object that implements a notify() method.
     * @return void
     */
    public function addListener ($listener)
    {
        if (! is_callable(array($listener, 'notify'))) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): listener does not '.
                'implement a notify() method.', E_USER_WARNING);
            return;
        }
        $this->listenerList[] = $listener;
    }


    /**
     * @param boolean $value
     * @return void
     */
    public function setRealtimeReporting($value = true) {
        $this->realtimeReporting = (boolean) $value;
    }


    /**
     * @param boolean $value
     * @return void
     */
    public function setDieOnError ($value = true) {
        $this->dieOnError = (boolean) $value;
    }


    /**
     * @param int $value
     * @return void
     */
    public function setErrorReportingLevel ($value = null) {
        if (!isset($value)) {
            $this->errorReportingLevel = ini_get('error_reporting');
        } else {
            $this->errorReportingLevel = $value;
        }
    }


    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     */
    public function pushError ($errno, $errstr = null, $errfile = null, $errline = null) {
        $errorData = $this->normalizeErrorData($errno, $errstr, $errfile, $errline);
        if (! ($errorData['level'] & $this->errorReportingLevel)) {
            return;
        }
        $this->errorStack[] = $errorData;
        if ($this->realtimeReporting) {
            $format = ESYS_ERRORREPORTER_CLI 
                ? '%s'
                : "<pre style=\"background-color: #F00; color: #FFF;\">%s</pre>\n";
            echo sprintf($format, $this->errorToString($this->getLast()));
        }
        $this->notifyListeners($this->getLast());
        if ($this->dieOnError) {
            exit(__CLASS__.": error found -- terminating\n");
        }
    }


    /**
     * @return boolean
     */
    public function hasErrors () {
        return (count($this->errorStack) ? true : false);
    }


    /**
     * @return string
     */
    public function report () {
        $string =  "ERROR REPORT\n";
        $string .= "============\n\n";
        foreach ($this->errorStack as $i => $error) {
            $string .= $this->errorToString($error, $i);
        }
        $string .= "\n";
        return $string;
    }


    private function observePhpErrors () {
        set_error_handler(array($this, 'pushError'));
    }


    private function normalizeErrorData ($errno, $errstr = null, $errfile = null, $errline = null)
    {
        $errorData = array(
            'level' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'backtrace' => null,
            'peartype' => null,
        );
        if (is_object($errno)) {        
            $pearError = $errno;  // assume first arg is a PEAR_Error
            $errorData['level'] = $pearError->level;
            $errorData['message'] = $pearError->getMessage();
            $errorData['file'] = $backtrace[0]['file'];
            $errorData['line'] = $backtrace[0]['line'];
            $errorData['backtrace'] = $pearError->getBacktrace();
            $errorData['peartype'] = $pearError->getType();
        }
        if(is_null($errorData['backtrace'])) {
            $errorData['backtrace'] = debug_backtrace();
            array_shift($errorData['backtrace']);
        }
        foreach ($errorData['backtrace'] as $i => $trace) {
            unset($errorData['backtrace'][$i]['args']);
        }
        return $errorData;
    }


    private function notifyListeners ($errorData)
    {
        $notification = array(
            'source' => get_class($this),
            'description' => $errorData['message'],
            'data' => $errorData,
        );
        foreach ($this->listenerList as $listener) {
            $listener->notify($notification);
        }
    }


    private function getLast() {
        return $this->errorStack[count($this->errorStack) - 1];
    }


    private function errorToString ($error, $index = null) {
        $string = '';    
        switch ($error['level']) {
            case (E_USER_NOTICE): 
            case (E_NOTICE): 
                $level = 'notice';
                break;
            case (E_WARNING): 
            case (E_USER_WARNING): 
                $level = 'warning';
                break;
            case (E_USER_ERROR): 
                $level = 'error';
                break;
            case (E_STRICT): 
                $level = 'strict';
                break;
            default:
                $level = 'unknown';
                break;                  
        }
        $string .= isset($index) ? "Error ".($index+1)." ($level)\n" : "[$level]: ";
        $string .= (isset($error['peartype'])) ? "PEAR type: {$error['peartype']}\n" : '';
        $string .= "\"{$error['message']}\"\n";
        $string .= "on line {$error['line']} of file {$error['file']}\n";
        $string .= "Full Backtrace:\n";
        $string .= $this->backtraceToString($error['backtrace'])."\n";
        return $string;
    }


    private function backtraceToString ($btArray) {
        $string = '';
        $btArray = array_reverse($btArray);
        foreach ($btArray as $trace) {
            if (! array_key_exists('line', $trace)
                || ! array_key_exists('file', $trace))
            {
                continue;
            }
            $string .= "\t" . 'on line '.$trace['line'] .
                ' in file '.$trace['file'] . "\n";
        }
        return $string;
    }


}


?>