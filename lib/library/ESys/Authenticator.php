<?php

/**
 * @package ESys
 */
class ESys_Authenticator {


    private $credentialsChecker;
    

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
