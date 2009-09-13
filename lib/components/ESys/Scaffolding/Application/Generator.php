<?php

require_once 'ESys/Scaffolding/SourceFileWriter.php';
require_once 'ESys/Scaffolding/SourceTemplate.php';


/**
 * @package ESys
 */
class ESys_Scaffolding_Application_Generator {


    protected $templateDir;


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
        $fileWriter = new ESys_Scaffolding_SourceFileWriter();
        if (! $fileWriter->setBaseDirectory($targetPath)) {
            return false;
        }
        echo "building layout template...\n";
        if (! $this->generateMainTemplate($packageName, $fileWriter)) {
            return false;
        }
        echo "building font controller script...\n";
        if (! $this->generateFrontControllerScript($packageName, $fileWriter)) {
            return false;
        }
        echo "building htaccess file...\n";
        if (! $this->generateHtaccess($packageName, $fileWriter)) {
            return false;
        }
        return true;
    }


    /**
     * @param string
     * @return boolean
     */
    protected function generateMainTemplate ($packageName, 
        ESys_Scaffolding_SourceFileWriter $fileWriter) 
	{
		$template = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/main-template.tpl.php');
		$template->set('packageName', $packageName);
		$filePath = $packageName.'/AdminApp/templates/main.tpl.php';
		if (! $fileWriter->write($filePath, $template->fetch())) {
		    return false;
		}
        return true;		
	}
    

	/**
     * @param string
     * @return boolean
	 */
	protected function generateFrontControllerScript ($packageName,
        ESys_Scaffolding_SourceFileWriter $fileWriter) 	
	{
		$template = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/front-controller-script.tpl.php');
		$template->set('packageName', $packageName);
		$filePath = $packageName.'/AdminApp/www/index.php';
		if (! $fileWriter->write($filePath, $template->fetch())) {
		    return false;
		}
        return true;		
	}


	/**
     * @param string
     * @return boolean
	 */
	protected function generateHtaccess ($packageName,
        ESys_Scaffolding_SourceFileWriter $fileWriter)
	{
		$template = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/htaccess.tpl.php');
		$filePath = $packageName.'/AdminApp/www/htaccess';
		if (! $fileWriter->write($filePath, $template->fetch())) {
		    return false;
		}
        return true;		
	}


}