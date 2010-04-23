<?php

require_once 'ESys/DB/Reflector.php';
require_once 'ESys/DB/Connection.php';
require_once 'ESys/Scaffolding/Entity/Entity.php';
require_once 'ESys/Scaffolding/SourceTemplate.php';
require_once 'ESys/Scaffolding/SourceFileWriter.php';
require_once 'ESys/Scaffolding/Package.php';

class ESys_Scaffolding_Entity_Generator {


	private $templateDir;


	public function __construct ()
	{
	    $this->templateDir = dirname(__FILE__).'/templates';
	}


    public function generate ($package, $tableName, $targetPath)
    {
        $fileWriter = new ESys_Scaffolding_SourceFileWriter();
        if (! $fileWriter->setBaseDirectory($targetPath)) {
            return false;
        }

        $package = new ESys_Scaffolding_Package($package);
        
        echo "getting database connection...\n";
        if (! $db = $this->getDatabaseConnection()) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() no database connection available.",
                E_USER_WARNING);
            return false;
        }
        echo "intializing entity...\n";
        if (! $entity = $this->createEntity($tableName, $package->base(), $db)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() unable to prepare entity.",
                E_USER_WARNING);
            return false;
        }
        echo "building controller...\n";
        if (! $this->generateController($entity, $package, $fileWriter)) {
            return false;
        }
        echo "building model...\n";
        if (! $this->generateModel($entity, $package, $fileWriter)) {
            return false;
        }
        echo "building form...\n";
        if (! $this->generateForm($entity, $package, $fileWriter)) {
            return false;
        }
        echo "building list view...\n";
        if (! $this->generateListView($entity, $package, $fileWriter)) {
            return false;
        }
        echo "building form view...\n";
        if (! $this->generateFormView($entity, $package, $fileWriter)) {
            return false;
        }
        return true;
    }


	protected function generateModel ($entity, $package, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/model.tpl.php');
		$view->set('entity', $entity);
		$modelSource = $view->fetch();
		$modelFilePath = $entity->fileName();
		if (! $fileWriter->write($modelFilePath, $modelSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateForm ($entity, $package, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/form.tpl.php');
		$view->set('entity', $entity);
		$view->set('package', $package);
		$formSource = $view->fetch();
		$formFilePath = $this->getAdminPackagePath($package, $entity).'/Form.php';
		if (! $fileWriter->write($formFilePath, $formSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateController ($entity, $package, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/controller.tpl.php');
		$view->set('entity', $entity);
		$view->set('package', $package);
		$controllerSource = $view->fetch();
		$controllerFilePath = $this->getAdminPackagePath($package, $entity).'/Controller.php';
		if (! $fileWriter->write($controllerFilePath, $controllerSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateListView ($entity, $package, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/list-view.tpl.php');
		$view->set('entity', $entity);
		$listViewSource = $view->fetch();
		$listViewFilePath = $this->getAdminPackagePath($package, $entity).'/templates/list.tpl.php';
		if (! $fileWriter->write($listViewFilePath, $listViewSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateFormView ($entity, $package, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/form-view.tpl.php');
		$view->set('entity', $entity);
		$formViewSource = $view->fetch();
		$formViewFilePath = $this->getAdminPackagePath($package, $entity).'/templates/form.tpl.php';
		if (! $fileWriter->write($formViewFilePath, $formViewSource)) {
		    return false;
		}
        return true;		
	}


	protected function createEntity ($tableName, $package, $db)
	{
		$reflector = new ESys_DB_Reflector($db);
		$table = $reflector->fetchTables($tableName);
		if (! $table) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.
			    '(): requested table does not exist', E_USER_ERROR);
			return false;
		}
		return new ESys_Scaffolding_Entity_Entity($table, $package.'_Domain');
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


    protected function getAdminPackagePath ($package, $entity)
    {
		$adminPackageName = $package->full().'_'.ucfirst($entity->instanceName());
		return str_replace('_', '/', $adminPackageName);
    }


}