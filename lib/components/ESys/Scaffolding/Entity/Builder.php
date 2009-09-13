<?php

require_once 'ESys/DB/Reflector.php';
require_once 'ESys/DB/Connection.php';
require_once 'ESys/Scaffolding/Entity/Entity.php';

class ESys_Scaffolding_Entity_Builder {


	private $templateDir;


	public function __construct ()
	{
	    $this->templateDir = dirname(__FILE__).'/templates/builder';
	}


    public function generate ($packageName, $tableName, $targetPath)
    {
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

        echo "getting database connection...\n";
        if (! $db = $this->getDatabaseConnection()) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() no database connection available.",
                E_USER_WARNING);
            return false;
        }

        echo "intializing entity...\n";
        if (! $entity = $this->createEntity($tableName, $packageName, $db)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() unable to prepare entity.",
                E_USER_WARNING);
            return false;
        }

        echo "building controller...\n";
        if (! $this->generateController($entity, $targetPath)) {
            return false;
        }
        echo "building model...\n";
        if (! $this->generateModel($entity, $targetPath)) {
            return false;
        }
        echo "building form...\n";
        if (! $this->generateForm($entity, $targetPath)) {
            return false;
        }
        echo "building list view...\n";
        if (! $this->generateListView($entity, $targetPath)) {
            return false;
        }
        echo "building form view...\n";
        if (! $this->generateFormView($entity, $targetPath)) {
            return false;
        }
        
        return true;
    }


	protected function generateModel ($entity, $targetPath) 
	{
		$view = new ESys_Template($this->templateDir.'/model.tpl.php');
		$view->set('entity', $entity);
		$modelSource = self::parsePhpTags($view->fetch());
		$modelFilePath = $targetPath.'/'.$entity->fileName();
		if (! $this->writeSourceFile($modelFilePath, $modelSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateForm ($entity, $targetPath) 
	{
		$view = new ESys_Template($this->templateDir.'/form.tpl.php');
		$view->set('entity', $entity);
		$formSource = self::parsePhpTags($view->fetch());
		if (! $this->prepareTargetSubDirectory(
		    $targetPath, $this->getAdminPackagePath($entity))) 
		{
		    return false;
		}
		$formFilePath = $targetPath.'/'.$this->getAdminPackagePath($entity).'/Form.php';
		if (! $this->writeSourceFile($formFilePath, $formSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateController ($entity, $targetPath) 
	{
		$view = new ESys_Template($this->templateDir.'/controller.tpl.php');
		$view->set('entity', $entity);
		$controllerSource = self::parsePhpTags($view->fetch());
		if (! $this->prepareTargetSubDirectory(
		    $targetPath, $this->getAdminPackagePath($entity))) 
		{
		    return false;
		}
		$controllerFilePath = $targetPath.'/'.$this->getAdminPackagePath($entity).'/Controller.php';
		if (! $this->writeSourceFile($controllerFilePath, $controllerSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateListView ($entity, $targetPath) 
	{
		$view = new ESys_Template($this->templateDir.'/list-view.tpl.php');
		$view->set('entity', $entity);
		$listViewSource = self::parsePhpTags($view->fetch());
		$listViewFilePath = $targetPath.'/'.$this->getAdminPackagePath($entity).'/templates/list.tpl.php';
		if (! $this->prepareTargetSubDirectory(
		    $targetPath, $this->getAdminPackagePath($entity).'/templates')) 
		{
		    return false;
		}
		if (! $this->writeSourceFile($listViewFilePath, $listViewSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateFormView ($entity, $targetPath) 
	{
		$view = new ESys_Template($this->templateDir.'/form-view.tpl.php');
		$view->set('entity', $entity);
		$formViewSource = self::parsePhpTags($view->fetch());
		$formViewFilePath = $targetPath.'/'.$this->getAdminPackagePath($entity).'/templates/form.tpl.php';
		if (! $this->prepareTargetSubDirectory(
		    $targetPath, $this->getAdminPackagePath($entity).'/templates')) 
		{
		    return false;
		}
		if (! $this->writeSourceFile($formViewFilePath, $formViewSource)) {
		    return false;
		}
        return true;		
	}


	protected function createEntity ($tableName, $packageName, $db)
	{
		$reflector = new ESys_DB_Reflector($db);
		$table = $reflector->fetchTables($tableName);
		if (! $table) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.
			    '(): requested table does not exist', E_USER_ERROR);
			return false;
		}
		return new ESys_Scaffolding_Entity_Entity($table, $packageName);
	}


	public static function pluralize ($string)
	{
		$lastLetter = $string[strlen($string)-1];
		switch ($lastLetter) {
			case 'y':
				$string = substr_replace($string, 'ies', -1);
			break;
			case 's':
				$string = $string .= 'es';
			break;
			default :
				$string .= 's';
			break;
		}
		return $string;
	}


	protected static function parsePhpTags ($string) 
	{
		$string = str_replace('<php>', '<?php', $string);
		$string = str_replace('</php>', '?'.'>', $string);
		return $string;
	}


    protected function getDatabaseConnection ()
    {
        $conf = ESys_Application::get('config');
        $connection = new ESys_DB_Connection(
            $conf->get('databaseUser'),
            $conf->get('databasePassword'),
            $conf->get('databaseName')
        );
        if (! $connection->connect()) {
            $errorMessage = $connection->describeError(true);
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                "database connection failed. ".$errorMessage, E_USER_WARNING);
            return false;
        }
        return $connection;
    }



    protected function getAdminPackagePath ($entity)
    {
		$adminPackageName = $entity->packageName().'_AdminApp_'.ucfirst($entity->instanceName());
		return str_replace('_', '/', $adminPackageName);
    }



    protected function writeSourceFile ($fileName, $source)
    {
        if (file_exists($fileName)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                '(): unable to create file. file already exists.',
                E_USER_WARNING);
            return false;
        }
        if (file_put_contents($fileName, $source) === false) {
            return false;
        }
        chmod($fileName, 0666);
        return true;
    }



    protected function prepareTargetSubDirectory ($baseDirectory, $subDirectory)
    {
        $subDirectoryPartList = explode('/', $subDirectory);
        $targetDirectory = $baseDirectory;
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
        return true;
   }

}