<?php

require_once 'ESys/Authenticator.php';


class ESys_Admin_CredentialsChecker implements ESys_Authenticator_CredentialsChecker {


    public function __construct ($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }


    public function checkCredentials ($credentials)
    {
        $credentials = new ESys_ArrayAccessor($credentials);
        $credentials = $credentials->get(array(
            'username',
            'password',
        ));
        return ($this->username == $credentials['username']
            && $this->password == $credentials['password']);
    }


    public function checkAuthorization ($loginId, $authorizationId)
    {
        return ! empty($loginId);
    }


}
