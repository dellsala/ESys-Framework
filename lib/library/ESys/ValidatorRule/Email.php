<?php
/**
 * @package ESys
 */



/**
 * @package ESys
 */
class ESys_ValidatorRule_Email extends ESys_ValidatorRule
{

    private $checkDomain;

    /**
     * @param boolean $checkDomain
     */
    public function __construct ($checkDomain = false)
    {
        $this->checkDomain = $checkDomain;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return $this->validateEmail($value, $this->checkDomain);
    }


    /**
     * Validate an email address.
     *
     * Implementation simplified from PEAR::Validator.
     */
    private function validateEmail ($email, $checkDomain = false)
    {
        $regex = '&^(?:                                               # recipient:
         ("\s*(?:[^"\f\n\r\t\v\b\s]+\s*)+")|                          #1 quoted name
         ([-\w!\#\$%\&\'*+~/^`|{}]+(?:\.[-\w!\#\$%\&\'*+~/^`|{}]+)*)) #2 OR dot-atom
         @(((\[)?                     #3 domain, 4 as IPv4, 5 optionally bracketed
         (?:(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.){3}
               (?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))))(?(5)\])|
         ((?:[a-z0-9](?:[-a-z0-9]*[a-z0-9])?\.)*[a-z0-9](?:[-a-z0-9]*[a-z0-9])?)  #6 domain as hostname
         \.((?:([^- ])[-a-z]*[-a-z])?)) #7 TLD 
         $&xi';
        if (preg_match($regex, $email)) {
            if ($checkDomain && function_exists('checkdnsrr')) {
                list (, $domain)  = explode('@', $email);
                if (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A')) {
                    return true;
                }
                return false;
            }
            return true;
        }
        return false;
    }

}

