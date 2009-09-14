<?php

$entity = $this->getRequired('entity');
$package = $this->getRequired('package');


$adminPackageName = $package->full().'_'.ucfirst($entity->instanceName());



?>
<php>

require_once 'ESys/WebControl/Controller.php';
require_once '<?php echo str_replace('_', '/', $adminPackageName.'_Form').'.php'; ?>';
require_once '<?php echo $entity->fileName(); ?>';

class <?php echo $adminPackageName; ?>_Controller extends ESys_WebControl_Controller {


    private $templateDir;
    
    
    public function __construct ()
    {
        $this->templateDir = dirname(__FILE__).'/templates';
    }
    

    protected function isAuthorized (ESys_WebControl_Request $request)
    {
        $auth = ESys_Application::get('authenticator');
        return $auth->isAuthorized();
    }


    protected function doIndex ($request)
    {
        $<?php echo $entity->instanceName(); ?>Store = new <?php echo $entity->className(); ?>_DataStore();
        $<?php echo $entity->instanceName(); ?>List = $<?php echo $entity->instanceName(); ?>Store->fetchAll();
        $listView = new ESys_Template($this->templateDir.'/list.tpl.php');
        $listView->set('request', $request);
        $listView->set('<?php echo $entity->instanceName(); ?>List', $<?php echo $entity->instanceName(); ?>List);
        return $this->getResponseFactory()->build('ok', array(
            'content' => $listView->fetch()
        ));
    }


    protected function doNew ($request)
    {
        $form = new <?php echo $adminPackageName; ?>_Form();
        return $this->getResponseFactory()->build('ok', array(
            'content' => $form->render($request)
        ));
    }


    protected function doEdit ($request)
    {
        $params = $request->actionParameters();
        $id = isset($params[1]) ? $params[1] : null;
        if (! isset($id)) {
            return new ESys_WebControl_Response_Redirect($request->url('controller').'/new');
        }
        $<?php echo $entity->instanceName(); ?>Store = new <?php echo $entity->className(); ?>_DataStore();
        if (! $<?php echo $entity->instanceName(); ?> = $<?php echo $entity->instanceName(); ?>Store->fetch($id)) {
            return $this->getResponseFactory()->build('notFound', array(
                'content' => "<?php echo ucfirst($entity->displayName()); ?> {$id} not found"
            ));
        }
        $form = new <?php echo $adminPackageName; ?>_Form();
        $form->captureInput($<?php echo $entity->instanceName(); ?>->getAll());
        return $this->getResponseFactory()->build('ok', array(
            'content' => $form->render($request)
        ));
    }


    protected function doSave ($request)
    {
        $postData = $request->postData();
        $id = isset($postData['id']) ? $postData['id'] : null;
        $<?php echo $entity->instanceName(); ?>Store = new <?php echo $entity->className(); ?>_DataStore();
        $<?php echo $entity->instanceName(); ?> = empty($id) ? $<?php 
            echo $entity->instanceName(); ?>Store->fetchNew() : $<?php 
            echo $entity->instanceName(); ?>Store->fetch($id);
        if (! $<?php echo $entity->instanceName(); ?>) {
            return $this->getResponseFactory()->build('notFound', array(
                'content' => "<?php echo ucfirst($entity->displayName()); ?> record {$id} not found"
            ));
        }
        $form = new <?php echo $adminPackageName; ?>_Form();
        $form->captureInput($postData);
        if (! $form->validate()) {
            return $this->getResponseFactory()->build('ok', array(
                'content' => $form->render($request)
            ));
        }
        $<?php echo $entity->instanceName(); ?>->setAll($form->getData());
        if (! $<?php echo $entity->instanceName(); ?>Store->save($<?php echo $entity->instanceName(); ?>)) {
            return $this->getResponseFactory()->build('error', array());
        }
        return new ESys_WebControl_Response_Redirect($request->url('controller'));
    }


    protected function doDelete ($request)
    {
        $postData = $request->postData();
        $id = isset($postData['id']) ? $postData['id'] : null;
        $<?php echo $entity->instanceName(); ?>Store = new <?php echo $entity->className(); ?>_DataStore();
        if (! $<?php echo $entity->instanceName(); ?> = $<?php echo $entity->instanceName(); ?>Store->fetch($id)) {
            return $this->getResponseFactory()->build('notFound', array(
                'content' => "<?php echo ucfirst($entity->displayName()); ?> record {$id} not found"
            ));
        }
        if (! $<?php echo $entity->instanceName(); ?>Store->delete($<?php echo $entity->instanceName(); ?>)) {
            return $this->getResponseFactory()->build('error', array());
        }
        return new ESys_WebControl_Response_Redirect($request->url('controller'));
    }


    protected function commonResponseData ()
    {
        return array(
            'title' => '<?php echo ucfirst($entity->displayName()); ?> Tools',
        );
    }


}
