<?php

/**
 * @package ESys
 */
class ESys_Authenticator {


    private $credentialsChecker;
    

    private $denyAccessBehavior;
    

    private $session;
    
    
    /**
     * @param ESys_Authenticator_CredentialsChecker $credentialsChecker
     * @param ESys_Session $session
     */
    public function __construct (
        ESys_Authenticator_CredentialsChecker $credentialsChecker, ESys_Session $session)
    {
        $this->credentialsChecker = $credentialsChecker;
        $this->session = $session;
    }

    
    /**
     * @param ESys_Authenticator_DenyAccessBehavior $behavior
     * @return void
     */
    public function setDenyAccessBehavior (
        ESys_Authenticator_DenyAccessBehavior $behavior)
    {
        $this->denyAccessBehavior = $behavior;
    }


    /**
     * Begins a logged-in session if the supplied credentials are
     * accepted by the system.
     *
     * Credentials can be any free-form data that the supplied
     * ESys_Authenticator_CredentialsChecker knows how to handle.
     *
     * @param mixed $credentials
     * @return boolean
     */
    public function login ($credentials)
    {
        $loginId = $this->credentialsChecker->checkCredentials($credentials);
        if ($loginId) {
            $this->session->set(__CLASS__, 'loginId', $loginId);
            $this->session->regenerateId();
        }
        return $loginId;
    }


    /**
     * Re-checks credentials against the currently logged in users.
     *
     * @param mixed $credentials
     * @return boolean
     */
    public function verify ($credentials)
    {
        $currentLoginId = $this->getLoginId();
        if (! $currentLoginId) {
            return false;
        }
        $loginId = $this->credentialsChecker->checkCredentials($credentials);
        return $currentLoginId == $loginId;
    }


    /**
     * Checks if access is allowed against an authorization id.
     *
     * @param string $authorizationId 
     * @return boolean
     */
    public function isAuthorized ($authorizationId = null)
    {
        return $this->credentialsChecker->checkAuthorization(
            $this->getLoginId(), $authorizationId);
    }


    /**
     * Checks authorization and denys access if authorization fails.
     *
     * @param string $authorizationId 
     * @return void
     */
    public function filterAccess ($authorizationId = null)
    {
        if (! $this->isAuthorized($authorizationId)) {
            $this->denyAccess();
            exit();
        }
        return;
    }



    /**
     * Denys access to the user.
     *
     * Exact behavior is determined by the denyAccessBehavior
     * assigned to the authenticator.
     *
     * @return void
     */
    public function denyAccess ()
    {
        $denyAccessBehavior = isset($this->denyAccessBehavior)
            ? $this->denyAccessBehavior
            : new ESys_Authenticator_DenyAccessBehavior_Default();
        $denyAccessBehavior->denyAccess($this->isLoggedIn());
    }



    /**
     * Logs out the current session.
     *
     * @return void
     */
    public function logout ()
    {
        $this->session->delete(__CLASS__, 'loginId');
        $this->session->regenerateId();
    }


    /**
     * @return boolean
     */
    public function isLoggedIn ()
    {
        return (boolean) $this->getLoginId();
    }


    /**
     * Provides the ID of the logged in session.
     *
     * Typically, this will be return user id of some kind.
     *
     * @return string|false
     */
    public function getLoginId ()
    {
        return $this->session->get(__CLASS__, 'loginId');
    }


}



/**
 * @package ESys
 */
interface ESys_Authenticator_CredentialsChecker {

    /**
     * @param mixed $credentials
     * @return boolean
     */
    public function checkCredentials ($credentials);

    /**
     * @param string $loginId
     * @param mixed $authorizationId
     * @return boolean
     */
    public function checkAuthorization ($loginId, $authorizationId);

}


/**
 * @package ESys
 */
interface ESys_Authenticator_DenyAccessBehavior {

    /**
     * @param boolean $isLoggedIn
     */
    public function denyAccess ($isLoggedIn);

}


/**
 * @package ESys
 */
class ESys_Authenticator_DenyAccessBehavior_Default 
    implements ESys_Authenticator_DenyAccessBehavior
{

    /**
     * @param boolean $isLoggedIn
     * @return void
     */
    public function denyAccess ($isLoggedIn)
    {
        header('HTTP/1.x 403 Forbidden');
        echo $isLoggedIn ? 'Not authorized.' : 'Not logged in.';
        exit();
    }

}