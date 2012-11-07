<?php

define('ESYS_FILE_WIN32', defined('OS_WINDOWS') 
    ? OS_WINDOWS 
    : !strncasecmp(PHP_OS, 'win', 3));

/**
 * @package ESys
 */
class ESys_File_Util {

    /**
     * Converts a number of bytes into a human
     * readable filesize as B, KB, GB, etc...
     * 
     * @param int $bytes
     * @return string
     */
    public static function humanSize ($bytes)
    {
        if ($bytes < 1024) {
            $unit = "B";
            $value = $bytes;
        } else if ($bytes < 1048576) {
            $unit = "Kb";
            $value = round($bytes / 1024, 2);
        } else if ($bytes < 1073741824) {
            $unit = "MB";
            $value = round($bytes / 1048576, 2);
        } else if ($bytes < 1099511627776) {
            $unit = "GB";
            $value = round($bytes / 1073741824, 2);
        } else {
            $unit = "TB";
            $value = round($bytes / 1099511627776, 2);
        }
        return $value.' '.$unit;
    }


    /**
     * Get real path (works with non-existant paths)
     *
     * @param   string $path
     * @param   string $separator
     * @return  string
     */
    public static function realPath ($path, $separator = DIRECTORY_SEPARATOR)
    {
        if (!strlen($path)) {
            return $separator;
        }
        $drive = '';
        if (ESYS_FILE_WIN32) {
            $path = preg_replace('/[\\\\\/]/', $separator, $path);
            if (preg_match('/([a-zA-Z]\:)(.*)/', $path, $matches)) {
                $drive = $matches[1];
                $path  = $matches[2];
            } else {
                $cwd   = getcwd();
                $drive = substr($cwd, 0, 2);
                if ($path{0} !== $separator{0}) {
                    $path  = substr($cwd, 3) . $separator . $path;
                }
            }
        } elseif ($path{0} !== $separator) {
            $path = getcwd() . $separator . $path;
        }
        $dirStack = array();
        foreach (explode($separator, $path) as $dir) {
            if (strlen($dir) && $dir !== '.') {
                if ($dir == '..') {
                    array_pop($dirStack);
                } else {
                    $dirStack[] = $dir;
                }
            }
        }
        return $drive . $separator . implode($separator, $dirStack);
    }


    /**
     * Get path relative to another path
     *
     * @param   string $path
     * @param   string $root
     * @param   string $separator
     * @return  string
     */
    public static function relativePath($path, $root, $separator = DIRECTORY_SEPARATOR)
    {
        $path = File_Util::realpath($path, $separator);
        $root = File_Util::realpath($root, $separator);
        $dirs = explode($separator, $path);
        $comp = explode($separator, $root);

        if (ESYS_FILE_WIN32) {
            if (strcasecmp($dirs[0], $comp[0])) {
                return $path;
            }
            unset($dirs[0], $comp[0]);
        }
        foreach ($comp as $i => $part) {
            if (isset($dirs[$i]) && $part == $dirs[$i]) {
                unset($dirs[$i], $comp[$i]);
            } else {
                break;
            }
        }
        return str_repeat('..' . $separator, count($comp)) . implode($separator, $dirs);
    }


    /**
     * @param string $filePath
     * @return string
     */
    public static function mimeType ($filePath)
    {
        $mimeTypeByExtension = array(
            'ai' => 'application/postscript',
            'aif' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'asf' => 'video/x-ms-asf',
            'asx' => 'video/x-ms-asf',
            'au' => 'audio/basic',
            'avi' => 'video/x-msvideo',
            'bcpio' => 'application/x-bcpio',
            'bin' => 'application/octet-stream',
            'bmp' => 'image/x-xbitmap',
            'cdf' => 'application/x-netcdf',
            'class' => 'application/octet-stream',
            'cpio' => 'application/x-cpio',
            'cpt' => 'application/mac-compactpro',
            'csh' => 'application/x-csh',
            'css' => 'text/css',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dll' => 'application/octet-stream',
            'dms' => 'application/octet-stream',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dtd' => 'text/xml',
            'dvi' => 'application/x-dvi',
            'dwg' => 'application/octet-stream',
            'dxr' => 'application/x-director',
            'eps' => 'application/postscript',
            'etx' => 'text/x-setext',
            'evy' => 'application/x-envoy',
            'exe' => 'application/octet-stream',
            'fif' => 'application/fractals',
            'fli' => 'application/octet-stream',
            'gif' => 'image/gif',
            'gl' => 'application/octet-stream',
            'gtar' => 'application/x-gtar',
            'gz' => 'application/x-gzip',
            'hdf' => 'application/x-hdf',
            'hpx' => 'application/mac-binhex40',
            'hqx' => 'application/mac-binhex40',
            'htm' => 'text/html',
            'html' => 'text/html',
            'ice' => 'x-conference/x-cooltalk',
            'ief' => 'image/ief',
            'iges' => 'model/iges',
            'igs' => 'model/iges',
            'isv' => 'bws-internal/intrasrv-urlencoded',
            'jfm' => 'bws-internal/intrasrv-form',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jrp' => 'bws-internal/intrasrv-report',
            'js' => 'application/x-javascript',
            'kar' => 'audio/midi',
            'latex' => 'application/x-latex',
            'lha' => 'application/octet-stream',
            'ls' => 'application/x-javascript',
            'lzh' => 'application/octet-stream',
            'man' => 'application/x-troff-man',
            'me' => 'application/x-troff-me',
            'mesh' => 'model/mesh',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mif' => 'application/x-mif',
            'mocha' => 'application/x-javascript',
            'mov' => 'video/quicktime',
            'movie' => 'video/x-sgi-movie',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'mpe' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpga' => 'audio/mpeg',
            'ms' => 'application/x-troff-ms',
            'msh' => 'model/mesh',
            'nc' => 'application/x-netcdf',
            'oda' => 'application/oda',
            'pac' => 'application/x-ns-proxy-autoconfig',
            'pbm' => 'image/x-portable-bitmap',
            'pdb' => 'chemical/x-pdb',
            'pdf' => 'application/pdf',
            'pgm' => 'image/x-portable-graymap',
            'php' => 'application/x-httpd-php',
            'php3' => 'application/x-httpd-php3',
            'phtml-msql2' => 'application/x-httpd-php-msql2',
            'phtml' => 'application/x-httpd-php',
            'png' => 'image/png',
            'pnm' => 'image/x-portable-anymap',
            'ppm' => 'image/x-portable-pixmap',
            'ppt' => 'application/powerpoint',
            'ps' => 'application/postscript',
            'qt' => 'video/quicktime',
            'ra' => 'audio/x-realaudio',
            'ram' => 'audio/x-pn-realaudio',
            'ras' => 'image/x-cmu-raster',
            'rgb' => 'image/x-rgb',
            'roff' => 'application/x-troff',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'rtf' => 'application/rtf',
            'rtx' => 'text/richtext',
            'scm' => 'application/octet-stream',
            'sgm' => 'text/x-sgml',
            'sgml' => 'text/x-sgml',
            'sh' => 'application/x-sh',
            'shar' => 'application/x-shar',
            'silo' => 'model/mesh',
            'sit' => 'application/stuffit',
            'sit' => 'application/x-stuffit',
            'skd' => 'application/x-koan',
            'skm' => 'application/x-koan',
            'skp' => 'application/x-koan',
            'skt' => 'application/x-koan',
            'snd' => 'audio/basic',
            'src' => 'application/x-wais-source',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'swf' => 'application/x-shockwave-flash',
            't' => 'application/x-troff',
            'tar' => 'application/x-tar',
            'tcl' => 'application/x-tcl',
            'tex' => 'application/x-tex',
            'texi' => 'application/x-texinfo',
            'texi' => 'application/x-textinfo',
            'texinfo' => 'application/x-textinfo',
            'text' => 'text/plain',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'tr' => 'application/x-troff',
            'tsp' => 'application/dsptype',
            'tsv' => 'text/tab-separated-values',
            'txt' => 'text/plain',
            'ustar' => 'application/x-ustar',
            'vcd' => 'application/x-cdlink',
            'vox' => 'audio/voxware',
            'vrml' => 'model/vrml',
            'wav' => 'audio/x-wav',
            'wax' => 'audio/x-ms-wax',
            'wm' => 'video/x-ms-wm',
            'wma' => 'audio/x-ms-wma',
            'wmd' => 'application/x-ms-wmd',
            'wmv' => 'video/x-ms-wmv',
            'wmx' => 'video/x-ms-wmx',
            'wmz' => 'application/x-ms-wmz',
            'wrl' => 'model/vrml',
            'wvx' => 'video/x-ms-wvx',
            'xbm' => 'image/x-xbitmap',
            'xml' => 'text/xml',
            'xpm' => 'image/x-xpixmap',
            'xwd' => 'image/x-xwindowdump',
            'xyz' => 'chemical/x-pdb',
            'z' => 'application/x-compress',
            'zip' => 'application/zip',
        );
        $pathInfo = pathinfo($filePath);
        $extension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : null;
        $mimeType = isset($mimeTypeByExtension[$extension])
            ? $mimeTypeByExtension[$extension]
            : 'application/octet-stream';
        return $mimeType;
    }


    /**
     * @param string $file
     * @return void
     */
    public static function httpPassthru ($file)
    {
        if (! is_object($file)) {
            $file = new ESys_File($file);
        }
        $mime = $file->mimeType();
        $fileSize = $file->size();
        $fileTimestamp = $file->lastModified();
        $fileETag = md5($file->path() . $fileSize . $fileTimestamp);
        $refreshContent = true;
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $cacheTimestamp = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($fileTimestamp <= $cacheTimestamp) {
                $refreshContent = false;
            }
        } else if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $cacheETag = $_SERVER['HTTP_IF_NONE_MATCH'];
            if ($fileETag == $cacheETag) {
                $refreshContent = false;
            }
        }
        if ($refreshContent) {
            header('Content-Type: '.$mime);
            header('Content-Length: '.$fileSize);
            header('Cache-Control: private');
            header('Pragma: ');            
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileTimestamp) . ' GMT');
            header("ETag: '".$fileETag."'");
            $file->contents(true);
        } else {
            header('HTTP/1.1 304 Not Modified');
            header("ETag: '".$fileETag."'");
            header('Cache-Control: private');
            header('Pragma: ');            
        }
    }

    
    /**
     * @param string $filePath
     * @return boolean
     */
    public static function isIncludable ($filePath)
    {
        if (file_exists($filePath)) {
            return true;
        }
        $includePath = ini_get('include_path');
        $includePathList = explode(PATH_SEPARATOR, $includePath);
        foreach ($includePathList as $path) {
            if (file_exists($path.'/'.$filePath)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param string $dataFile
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return array|false
     */
    public static function & loadCsv ($dataFile, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        if (! $fileResource = fopen($dataFile, 'r')) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): unable to open $dataFile ".
                "for reading.", E_USER_WARNING);
            $result = false;
            return $result;
        }
        $fields = fgetcsv($fileResource, 0, $delimiter, $enclosure);
        if (! $fields || is_null($fields[0])) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): $dataFile has no data.", 
                E_USER_WARNING);
            fclose($fileResource);
            $result = false;
            return $result;
        }
        $dataSet = array();
        while ($line = fgetcsv($fileResource, 0, $delimiter, $enclosure)) {
            $row = array();
            foreach ($fields as $fieldIndex => $fieldName) {
                if (! isset($line[$fieldIndex])) {
                    $row[$fieldName] = null;
                } else {
                    $row[$fieldName] = $line[$fieldIndex];
                }
            }
            $dataSet[] = $row;
        }
        fclose($fileResource);
        return $dataSet;
    }

}

