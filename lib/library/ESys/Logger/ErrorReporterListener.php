<?php

require_once 'ESys/Logger.php';

/**
 * @package ESys
 */
class ESys_Logger_ErrorReporterListener extends ESys_Logger
{


    protected $isLoggingBacktraces = false;


    public function isLoggingBacktraces ($value = null)
    {
        if (is_null($value)) {
            return $this->isLoggingBacktraces;
        }
        $this->isLoggingBacktraces = (boolean) $value;
    }


    /**
     * @param array $notification
     * @return void
     */
    public function notify ($notification)
    {
        if ($notification['source'] != 'ESys_ErrorReporter') {
            return;
        }
        $errorData = $notification['data'];
        switch ($errorData['level'])
        {
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
        $message = $errorData['message'].' in '.$errorData['file'].' on line '.
            $errorData['line'];
        if ($this->isLoggingBacktraces) {
            $message .= "\n".$this->backtraceToString($notification['data']['backtrace']);
        }
        $this->log("[{$level}] ".$message);
    }



    /**
     * @param array
     * @return string
     */
    protected function backtraceToString ($backtraceArray) 
    {
        $string = '';
        $backtraceArray = array_reverse($backtraceArray);
        foreach ($backtraceArray as $trace) {
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
