<?php

/**
 * @package ESys
 */
class ESys_Scaffolding_Application_Generator {


    protected $templateDir;
    protected $targetPath;


    public function __construct ()
    {
        $this->templateDir = dirname(__FILE__).'/templates';
    }


    /**
     * @param string
     * @param string
     * @return boolean
     */
    public function generate ($packageName, $targetPath)
    {
        $targetPath = rtrim($targetPath, '/');
        if (! is_dir($targetPath)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() target directory '{$targetPath}' does not exist.",
                E_USER_WARNING);
            return false;
        }
        if (! is_writable($targetPath)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() target directory '{$targetPath}' is not writable.",
                E_USER_WARNING);
            return false;
        }
        $this->targetPath = $targetPath;

        echo "building layout template...\n";
        if (! $this->generateMainTemplate($packageName)) {
            return false;
        }
        echo "building font controller script...\n";
        if (! $this->generateFrontControllerScript($packageName)) {
            return false;
        }
        echo "building htaccess file...\n";
        if (! $this->generateHtaccess($packageName)) {
            return false;
        }
        
        return true;
    }


    /**
     * @param string
     * @return boolean
     */
    protected function generateMainTemplate ($packageName) 
	{
		$template = new ESys_Template($this->templateDir.'/main-template.tpl.php');
		$template->set('packageName', $packageName);
		$source = $this->parsePhpTags($template->fetch());
		$filePath = $packageName.'/AdminApp/templates/main.tpl.php';
		if (! $this->writeSourceFile($filePath, $source)) {
		    return false;
		}
        return true;		
	}
    

	/**
     * @param string
     * @return boolean
	 */
	protected function generateFrontControllerScript ($packageName) 
	{
		$template = new ESys_Template($this->templateDir.'/front-controller-script.tpl.php');
		$template->set('packageName', $packageName);
		$source = $this->parsePhpTags($template->fetch());
		$filePath = $packageName.'/AdminApp/www/index.php';
		if (! $this->writeSourceFile($filePath, $source)) {
		    return false;
		}
        return true;		
	}


	/**
     * @param string
     * @return boolean
	 */
	protected function generateHtaccess ($packageName) 
	{
		$template = new ESys_Template($this->templateDir.'/htaccess.tpl.php');
		$source = $this->parsePhpTags($template->fetch());
		$filePath = $packageName.'/AdminApp/www/htaccess';
		if (! $this->writeSourceFile($filePath, $source)) {
		    return false;
		}
        return true;		
	}


	/**
	 * @param string
	 * @return string
	 */
	protected function parsePhpTags ($string) 
	{
		$string = str_replace('<php>', '<?php', $string);
		$string = str_replace('</php>', '?'.'>', $string);
		return $string;
	}


    /**
     * @param string
     * @param string
     * @return boolean
     */
    protected function writeSourceFile ($fileSubPath, $source)
    {

        $subDirectoryPartList = explode('/', dirname($fileSubPath));
        $targetDirectory = $this->targetPath;
        foreach ($subDirectoryPartList as $subDirectoryPart) {
            $targetDirectory .= '/'.$subDirectoryPart;
            if (! file_exists($targetDirectory) && ! mkdir($targetDirectory)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                    "failed to create directory ".$targetDirectory, E_USER_WARNING);
                return false;
            }
            if (! is_dir($targetDirectory)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                    "target directory {$targetDirectory} already exists as a file", E_USER_WARNING);
                return false;
            }
            chmod($targetDirectory, 0777);
        }
        $fileName = $this->targetPath .'/'.$fileSubPath;
        if (file_exists($fileName)) {
            echo "{$fileName} already exists.\nOverwrite it? (y/n) ";
            if (strtolower(trim(fgets(STDIN))) != 'y') {
                echo "File skipped.\n";
                return true;
            }
        }
        if (file_put_contents($fileName, $source) === false) {
            return false;
        }
        chmod($fileName, 0666);
        return true;
    }



}