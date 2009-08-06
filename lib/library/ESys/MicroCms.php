<?php

require_once 'ESys/AuthSimple.php';
require_once 'Textile.php';

/**
 * Simple edit-in-place content mangement system.
 *
 * <code>
 * //==== EXAMPLE CONFIGURATION ====//
 * // ideally this should be in a globally included file
 * 
 * ESys_MicroCms::init(array(
 *     'userFile' => '/path/to/users.ini',
 *     'storageDirectory' => '/some/path/to/data', 
 *     'markup' => 'textile'
 * ));
 * 
 * 
 * //==== EXAMPLE CONTENT BLOCK ====//
 * 
 * $contentBlock = new ESys_MicroCms::getBlock('name-of-block');
 * $contentBlock->process();
 * echo $contentBlock->render();
 * 
 * 
 * //==== EXAMPLE LOGIN CONTROLLER ====//
 * 
 * ESys_MicroCms::login();
 * </code>
 * 
 * @package ESys
 */
class ESys_MicroCms {

    /**
     * @param array $options
     */
    static public function init ($options)
    {
        $GLOBALS['ESYS_MICROCMS']['options'] = $options;
        ESys_AuthSimple::setOption('passwordFile', $options['userFile']);
        ESys_AuthSimple::setOption('sessionName', 'ESys_MicroCms');
        if (! isset($GLOBALS['ESYS_MICROCMS']['options']['format'])) {
        	$GLOBALS['ESYS_MICROCMS']['options']['format'] = 'textile';
        }
    }


    /**
     * @return boolean
     */
    public static function isLoggedIn ()
    {
        $auth =& ESys_AuthSimple::instance();
        return $auth->isLoggedIn();
    }


    /**
     * @return void
     */
    public static function login ()
    {
        $actionWhitelist = array(
            'prompt',
            'login',
            'logout'
        );
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        if (! isset($action) && ESys_MicroCms::isLoggedIn()) {
            echo ESys_MicroCms::_renderWelcome();
            return;
        }
        if (! in_array($action, $actionWhitelist)) {
            $action = 'prompt';
        }
        echo call_user_func(array('ESys_MicroCms', '_do'.ucfirst($action)), $_POST);
    }


    private function _doLogout ($postData)
    {
        $auth =& ESys_AuthSimple::instance();
        $auth->logout();
        $response = ESys_MicroCms::_renderLoggedOut();
        return $response;
    }


    private function _doLogin ($postData)
    {
        $auth =& ESys_AuthSimple::instance();
        $auth->logout();
        $username = isset($postData['username']) ? $postData['username'] : '';
        $password = isset($postData['password']) ? $postData['password'] : '';
        if ($auth->login($username, $password)) {
            $response = ESys_MicroCms::_renderWelcome();
        } else {
            $response = ESys_MicroCms::_renderForm($username, $auth->getLoginMessage());
        }
        return $response;
    }


    private function _doPrompt ($postData)
    {
        $auth =& ESys_AuthSimple::instance();
        $auth->logout();
        return ESys_MicroCms::_renderForm();
    }


    private function _renderForm ($username = null, $message = null)
    {
        $out = '<div class="microCmsLogin">';
        if (isset($message)) {
            $out .= '<p class="message">'.$message.'</p>';
        }
        $out .= '<form action="'.htmlentities($_SERVER['SCRIPT_NAME']).'?action=login" method="post">';
        $out .= '<table cellspacing="0">';
        $out .= '<tr>';
        $out .= '<td class="label">Username</td>';
        $out .= '<td><input type="text" name="username" value="'.htmlentities($username).'"></td>';
        $out .= '</tr>';
        $out .= '<tr>';
        $out .= '<td class="label">Password</td>';
        $out .= '<td><input type="password" name="password" value=""></td>';
        $out .= '</tr>';
        $out .= '<tr>';
        $out .= '<td class="label">&nbsp;</td>';
        $out .= '<td class="buttons"><input type="submit" value="Submit"></td>';
        $out .= '</tr>';
        $out .= '</table>';
        $out .= '</form>';
        $out .= '</div>';
        return $out;
    }


    private function _renderWelcome ()
    {
        $out = '<div class="microCmsLogin">';
        $out .= '<p>Welcome. You are logged in.</p>';
        $out .= '<p><a href="'.$_SERVER['SCRIPT_NAME'].'?action=logout">Logout</a></p>';
        $out .= '</div>';
        return $out;
    }


    private function _renderLoggedOut ()
    {
        $out = '<div class="microCmsLogin">';
        $out .= '<p>You have been logged out.</p>';
        $out .= '<p><a href="'.$_SERVER['SCRIPT_NAME'].'?action=prompt">Login</a></p>';
        $out .= '</div>';
        return $out;
    }



    /**
     * @param string $id
     * @return ESys_MicroCmsBlock
     */
    public static function getBlock ($id)
    {
        $contentBlock = new ESys_MicroCmsBlock($id);
        $contentBlock->setFormat($GLOBALS['ESYS_MICROCMS']['options']['format']);
        return $contentBlock;
    }


    /**
     * @return string
     */
    public static function getStorageDirectory()
    {
        return $GLOBALS['ESYS_MICROCMS']['options']['storageDirectory'];
    }


}


/**
 * @package ESys
 */
class ESys_MicroCmsBlock {

    private $id;
    private $data;
    private $message;
    private $view;  // public | form | preview
    private $format = 'textile';
    private $height = 300;
    private $width = null;

    /**
     * @param string $id
     */
    public function __construct ($id)
    {
        $this->id = $id;
    }


    /**
     * @param string $format
     * @return void
     */
    public function setFormat ($format)
    {
        $this->format = $format;
    }

    /**
     * @param int $width
     * @param int $height
     * @return void
     */
    public function setSize ($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }


    /**
     * process input, update file if required,
     * load data from file, report messages
     *
     * @return void
     */
    public function process ()
    {
        if (DSAla_MicroCms::isLoggedIn()) {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            if ($id != $this->id) {
                $action = null;
            }
            switch ($action)
            {
                case 'save':
                    $this->data = isset($_POST['content']) ? $_POST['content'] : null;
                    if (is_null($this->data)) {
                        $this->data = $this->_loadData();
                        $this->view = 'preview';
                    } else if (! $this->_saveData($this->data)) {
                        $this->view = 'form';
                        $this->message = 'Unexpected error while saving data.';
                    } else {
                        $this->data = $this->_loadData();
                        $this->view = 'preview';
                        $this->message = 'Saved.';
                    }
                    break;
                case 'edit':
                    $this->data = $this->_loadData();
                    $this->view = 'form';
                    break;
                default:
                    $this->data = $this->_loadData();
                    $this->view = 'preview';
                    break;
            }
        } else {
            $this->data = $this->_loadData();
            $this->view = 'public';
        }
    }


    /**
     * Renders the view and returns it as a string.
     *
     * @return string
     */
    public function render ()
    {
        return $this->{'_render'.ucfirst($this->view)}();
    }


    private function _loadData ()
    {
        $storageFile = ESys_MicroCms::getStorageDirectory().'/'.$this->id.'.data';
        if (! is_readable($storageFile)) {
            $data = '[ NO DATA ]';
        } else {
            $data = implode('', file($storageFile));
        }
        return $data;
    }


    private function _saveData ($data)
    {
        $storageFile = ESys_MicroCms::getStorageDirectory().'/'.$this->id.'.data';
        if (! file_exists($storageFile)) {
            if (is_writable(ESys_MicroCms::getStorageDirectory())) {
                touch($storageFile) && chmod($storageFile, 0666);
            }
        }
        $fh = @fopen($storageFile, 'w');
        if (! $fh) { return false; }
        fputs($fh, $data);
        fclose($fh);
        return true;
    }


    private function _renderPublic ()
    {
        if ($this->format == 'textile') {
            $textile = new ESys_Textile();
            $out = $textile->parse($this->data);
        } else {
            $out = $this->data;
        }
        return $out;
    }


    private function _renderPreview ()
    {
        $out = '';
        $out .= '<div class="microCmsBlock">';
        if (isset($this->message)) {
            $out .= '<div class="message">';
            $out .= htmlentities($this->message);
            $out .= '</div>';
        }
        if ($this->format == 'textile') {
            $textile = new ESys_Textile();
            $out .= $textile->parse($this->data);
        } else {
            $out .= $this->data;
        }
        $out .= '<div class="controls">';
        $out .= '<form action="'.$_SERVER['SCRIPT_NAME'].'?action=edit" method="post">';
        $out .= '<input type="hidden" name="id" value="'.htmlentities($this->id).'">';
        $out .= '<input type="submit" value="Edit">';
        $out .= '</form>';
        $out .= '</div>';
        $out .= '</div>';
        return $out;
    }


    private function _renderForm ()
    {
        $data = ($this->data == '[ NO DATA ]') ? '' : $this->data;
        $out = '';
        $out .= '<div class="microCmsBlock">';
        if (isset($this->message)) {
            $out .= '<div class="message">';
            $out .= htmlentities($this->message);
            $out .= '</div>';
        }
        $out .= '<div class="controls">';
        $out .= '<form action="'.$_SERVER['SCRIPT_NAME'].'?action=save" method="post">';
        $out .= '<input type="hidden" name="id" value="'.htmlentities($this->id).'">';
        if ($this->format == 'wysiwyg') {
            include_once 'ESys/FCKEditor.php';
            $editor = new ESys_FCKEditor('content');
            $editor->setToolbar('Basic');
            if (isset($this->width)) {
                $editor->setWidth($this->width);
            }
            if (isset($this->height)) {
                $editor->setHeight($this->height);
            }
            $editor->setValue($data);
            $out .= $editor->createHtml();
        } else {
            $cols = isset($this->width) ? $this->width / 8 : 60;
            $rows = isset($this->height) ? $this->height / 13 : 10;
            $out .= '<textarea name="content" cols="'.$cols.'" rows="'.$rows.'">'.
                $data.'</textarea><br>';
        }
        $out .= '<input type="submit" value="Save">';
        $out .= '</form>';
        $out .= '</div>';
        $out .= '</div>';
        return $out;
    }

}
