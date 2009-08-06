<?php

require_once 'ESys/Logger.php';

/**
 * @package ESys
 */
class ESys_Logger_DBListener extends ESys_Logger
{


    /**
     * @param array $notification
     * @return void
     */
    function notify ($notification)
    {
        $type = isset($notification['type']) ? $notification['type'] : null;
        switch ($type)
        {
            case (ESys_DB_Connection::EVENT_CONNECTION): 
                $type = 'CONNECTION';
                $message = $notification['message'];
                break;
            case (ESys_DB_Connection::EVENT_ERROR): 
                $type = 'ERROR';
                $message = "{$notification['code']}: {$notification['message']}";
                break;
            case (ESys_DB_Connection::EVENT_QUERY): 
                $type = 'QUERY';
                $message = $notification['query'];
                break;
            case (ESys_DB_Connection::EVENT_CLOSE): 
                $type = 'CLOSE';
                $message = $notification['message'];
                break;
            default:
                trigger_error(__CLASS__.'::'.__FUNCTION__.'() unrecognized event',
                    E_USER_NOTICE);
                return;
                break;                  
        }
        $this->log("[{$type}] ".str_replace("\n", " ", $message));
    }


}
