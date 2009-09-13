<?php

require_once 'ESys/DB/Reflector.php';
require_once 'ESys/DB/Connection.php';
require_once 'ESys/Scaffolding/Entity/Entity.php';
require_once 'ESys/Scaffolding/SourceTemplate.php';
require_once 'ESys/Scaffolding/SourceFileWriter.php';

class ESys_Scaffolding_Entity_Generator {


	private $templateDir;


	public function __construct ()
	{
	    $this->templateDir = dirname(__FILE__).'/templates';
	}


    public function generate ($packageName, $tableName, $targetPath)
    {
        $fileWriter = new ESys_Scaffolding_SourceFileWriter();
        if (! $fileWriter->setBaseDirectory($targetPath)) {
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
        if (! $this->generateController($entity, $fileWriter)) {
            return false;
        }
        echo "building model...\n";
        if (! $this->generateModel($entity, $fileWriter)) {
            return false;
        }
        echo "building form...\n";
        if (! $this->generateForm($entity, $fileWriter)) {
            return false;
        }
        echo "building list view...\n";
        if (! $this->generateListView($entity, $fileWriter)) {
            return false;
        }
        echo "building form view...\n";
        if (! $this->generateFormView($entity, $fileWriter)) {
            return false;
        }
        return true;
    }


	protected function generateModel ($entity, $fileWriter) 
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


	protected function generateForm ($entity, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/form.tpl.php');
		$view->set('entity', $entity);
		$formSource = $view->fetch();
		$formFilePath = $this->getAdminPackagePath($entity).'/Form.php';
		if (! $fileWriter->write($formFilePath, $formSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateController ($entity, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/controller.tpl.php');
		$view->set('entity', $entity);
		$controllerSource = $view->fetch();
		$controllerFilePath = $this->getAdminPackagePath($entity).'/Controller.php';
		if (! $fileWriter->write($controllerFilePath, $controllerSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateListView ($entity, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/list-view.tpl.php');
		$view->set('entity', $entity);
		$listViewSource = $view->fetch();
		$listViewFilePath = $this->getAdminPackagePath($entity).'/templates/list.tpl.php';
		if (! $fileWriter->write($listViewFilePath, $listViewSource)) {
		    return false;
		}
        return true;		
	}


	protected function generateFormView ($entity, $fileWriter) 
	{
		$view = new ESys_Scaffolding_SourceTemplate($this->templateDir.'/form-view.tpl.php');
		$view->set('entity', $entity);
		$formViewSource = $view->fetch();
		$formViewFilePath = $this->getAdminPackagePath($entity).'/templates/form.tpl.php';
		if (! $fileWriter->write($formViewFilePath, $formViewSource)) {
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


}