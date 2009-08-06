<?php


/**
 * @package ESys
 */
class ESys_MemoryChecker {


    private static $logFile;

    private static $notePrefix;


    /**
     * @param string $logFile
     * @param string $notePrefix
     * @return void
     */
    public static function start ($logFile, $notePrefix = null)
    {
        self::$logFile = $logFile;
        self::$notePrefix = $notePrefix;
        self::log('on start', memory_get_usage());
        register_shutdown_function(__CLASS__.'::checkPeak');
    }


    /**
     * @param string $note
     * @return void
     */
    public static function check ($note = 'runtime memory check')
    {
        self::log($note, memory_get_usage());
    }


    /**
     * @return void
     */
    public static function checkPeak ()
    {
        self::log('peak memory allocation', memory_get_peak_usage());
    }


    private static function log ($note, $bytes)
    {
        if (! isset(self::$logFile)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                __CLASS__.'::start() must be called first.', E_USER_ERROR);
            return;
        }
        $note = isset(self::$notePrefix)
            ? '['.self::$notePrefix.'] '.$note
            : $note;
        $message = sprintf("%s %15s bytes | %s\n", 
            date('Y-m-d H:i:s'), number_format($bytes), $note);
        error_log($message, 3, self::$logFile);
    }


}

