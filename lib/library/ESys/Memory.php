<?php
/**
 * @package ESys
 */

/**
 * Provides information about system memory.
 */
class ESys_Memory {


    protected $memoryInfo;
    

    public function __construct (ESys_Memory_GlobalSystemInfo $memoryInfo = null)
    {
        if (! $memoryInfo) {
            $memoryInfo = new ESys_Memory_GlobalSystemInfo();
        }
        $this->memoryInfo = $memoryInfo;
    }


    public function limit ()
    {
        $memoryLimitIniValue = $this->memoryInfo->limitIniValue();
        if (ctype_digit($memoryLimitIniValue)) {
            $memoryLimitInBytes = $memoryLimitIniValue;
        } else {
            $matches = array();
            if (! preg_match('/^(\d+)(\D+)$/', $memoryLimitIniValue, $matches)) {
                return (int) $memoryLimitIniValue;
            }
            $memoryValue = (int) $matches[1];
            switch (strtoupper($matches[2])) {
                case 'K':
                    $memoryLimitInBytes = $memoryValue * 1024;
                    break;
                case 'M':
                    $memoryLimitInBytes = $memoryValue * 1048576;
                    break;
                case 'G':
                    $memoryLimitInBytes = $memoryValue * 1.0737418240000e+9;
                    break;
                default:
                    $memoryLimitInBytes = $memoryValue;
                    break;
            }
        }
        return $memoryLimitInBytes;
    }
    

    public function available ()
    {
        return $this->limit() - $this->used();
    }


    public function used ()
    {
        return $this->memoryInfo->usage();
    }


    public function peak ()
    {
        return $this->memoryInfo->peak();
    }


}


/**
 * Abstraction of global memory api to facilitate testing.
 */
class ESys_Memory_GlobalSystemInfo {


    public function usage () 
    {
        return memory_get_usage();
    }


    public function limitIniValue () 
    {
        return ini_get('memory_limit');
    }

    public function peak () 
    {
        return memory_get_peak_usage();
    }


}