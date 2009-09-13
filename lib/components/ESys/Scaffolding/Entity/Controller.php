<?php


require_once 'ESys/Request/Handler.php';
require_once 'ESys/DB/Connection.php';
require_once 'ESys/CrudBuilder/Builder.php';

class ESys_CrudBuilder_Controller extends ESys_Request_Handler {


    private $templateDirectory = 'ESys/ModuleBuilder';
    

    protected function filterResponse ($response)
    {
        return $response."\n\n";
    }


    protected function doBuild (ESys_Request $request)
    {
        $params = $request->getActionParameters();
        $tableName = isset($params[0]) ? $params[0] : null;
        $packageName = isset($params[1]) ? $params[1] : null;
        $targetPath = isset($params[2]) ? $params[2] : '.';
        $missingInput = array();
        if (empty($packageName)) {
            $missingInput[] = "package name";
        }
        if (empty($tableName)) {
            $missingInput[] = "table name";
        }
        if (count($missingInput)) {
            $message = "INPUT ERROR: \n";
            foreach ($missingInput as $field) {
                $message .= "Missing {$field}.\n";
            }
            return $message;
        }

        if (! is_dir($targetPath)) {
            return "ERROR: Target directory '{$targetPath}' does not exist.";
        }

        if (! is_writable($targetPath)) {
            return "ERROR: Target directory '{$targetPath}' is not writable.";
        }
        
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
            return "ERROR: Database connection failed\n{$errorMessage}";
        }

        $builder = new ESys_CrudBuilder_Builder($connection);
        if (! $builder->prepareEntity($tableName, $packageName)) {
            return "ERROR: unable to prepare entity. Table `{$tableName}` does not exist.";
        }

        $controllerSource = $builder->generateController();
        $modelSource = $builder->generateModel();
        $listViewSource = $builder->generateListView();
        $formViewSource = $builder->generateFormView();

        
        $entity = $builder->getEntity();

        $packagePartList = explode('_', $entity->className().'Admin');
        $packagePartList[] = 'templates';
        $basePackagePath = $targetPath;
        foreach ($packagePartList as $packagePart) {
            $nextPath = $basePackagePath.'/'.$packagePart;
            if (! file_exists($nextPath)) {
                if (! mkdir($nextPath)) {
                    trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                        "failed to create package directory ".$nextPath, E_USER_WARNING);
                    return "ERROR: failed to create package directory {$nextPath}.";
                }
                chmod($nextPath, 0777);
            }
            $basePackagePath = $nextPath;
        }


        $adminPackagePath = $targetPath.'/'.substr($entity->fileName(), 0, 0 - strlen('.php')).'Admin';
        $modelFileName = $targetPath.'/'.$entity->fileName();
        $controllerFileName = $adminPackagePath.'/Controller.php';
        $listViewFileName = $adminPackagePath.'/templates/list.tpl.php';
        $formViewFileName = $adminPackagePath.'/templates/form.tpl.php';
        

        if (! $this->writeSourceFile($modelFileName, $modelSource)) {
            return "ERROR: failed to write model source file {$modelFileName}.";
        }
        if (! $this->writeSourceFile($controllerFileName, $controllerSource)) {
            return "ERROR: failed to write controller source file {$controllerFileName}.";
        }
        if (! $this->writeSourceFile($listViewFileName, $listViewSource)) {
            return "ERROR: failed to write list view source file {$listViewFileName}.";
        }
        if (! $this->writeSourceFile($formViewFileName, $formViewSource)) {
            return "ERROR: failed to write form view source file {$formViewFileName}.";
        }

        return "Done";
    }


    protected function doDefault (ESys_Request $request)
    {
        return $this->doBuild($request);
    }


    private function writeSourceFile ($fileName, $source)
    {
        if (file_exists($fileName)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): unable to create file. file already exists.',
                E_USER_WARNING);
            return false;
        }
        if (file_put_contents($fileName, $source) === false) {
            return false;
        }
        chmod($fileName, 0666);
        return true;
    }


}

