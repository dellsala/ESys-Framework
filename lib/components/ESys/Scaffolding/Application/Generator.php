<?php

require_once 'ESys/Scaffolding/SourceFileWriter.php';
require_once 'ESys/Scaffolding/SourceTemplate.php';
require_once 'ESys/Scaffolding/Package.php';


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
        
        $package = new ESys_Scaffolding_Package($packageName);
        
        echo "building layout template...\n";
        if (! $this->generateMainTemplate($package, $fileWriter)) {
            return false;
        }
        echo "building font controller script...\n";
        if (! $this->generateFrontControllerScript($package, $fileWriter)) {
            return false;
        }
        echo "building htaccess file...\n";
        if (! $this->generateHtaccess($package, $fileWriter)) {
            return false;
        }
        return true;
    }


    /**
     * @param ESys_Scaffolding_Package
     * @return boolean
     */
    protected function generateMainTemplate (ESys_Scaffolding_Package $package, 
        ESys_Scaffolding_SourceFileWriter $fileWriter) 
	{
		$template = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/main-template.tpl.php');
		$template->set('package', $package);
		$filePath = "{$package->base()}/{$package->sub()}/templates/main.tpl.php";
		if (! $fileWriter->write($filePath, $template->fetch())) {
		    return false;
		}
        return true;		
	}
    

	/**
     * @param ESys_Scaffolding_Package
     * @return boolean
	 */
	protected function generateFrontControllerScript (ESys_Scaffolding_Package $package,
        ESys_Scaffolding_SourceFileWriter $fileWriter) 	
	{
		$template = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/front-controller-script.tpl.php');
		$template->set('package', $package);
		$filePath = "{$package->base()}/{$package->sub()}/www/index.php";
		if (! $fileWriter->write($filePath, $template->fetch())) {
		    return false;
		}
        return true;		
	}


	/**
     * @param ESys_Scaffolding_Package
     * @return boolean
	 */
	protected function generateHtaccess (ESys_Scaffolding_Package $package,
        ESys_Scaffolding_SourceFileWriter $fileWriter)
	{
		$template = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/htaccess.tpl.php');
		$filePath = "{$package->base()}/{$package->sub()}/www/htaccess";
		if (! $fileWriter->write($filePath, $template->fetch())) {
		    return false;
		}
        return true;		
	}


}