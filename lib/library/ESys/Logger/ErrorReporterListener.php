<?php

require_once 'ESys/Logger.php';

/**
 * @package ESys
 */
class ESys_Logger_ErrorReporterListener extends ESys_Logger
{


    /**
     * @param array $notification
     * @return void
     */
    function notify ($notification)
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
        $this->log("[{$level}] ".$message);
    }


}
