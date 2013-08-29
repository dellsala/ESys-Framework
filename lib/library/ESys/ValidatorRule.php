<?php
/**
 * @package ESys
 */


/**
 * The validator rule interface used with the ESys_Validator class.
 *
 * @package ESys
 */
abstract class ESys_ValidatorRule
{

    protected $validator;

    /**
     * @param mixed $value
     * @return boolean
     */
    abstract public function validate ($value);

    /**
     * @param ESys_Validator $validator
     * @return void
     */
    public function setValidator ($validator) 
    {
        $this->validator = $validator;
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_MinLength extends ESys_ValidatorRule
{

    private $minLenth;

    /**
     * @param int $minLength
     */
    public function __construct ($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (is_array($value)) {
            return count($value) >= $this->minLength;
        }
        $value = (string) $value;
        return strlen($value) >= $this->minLength;
    }

}



/**
 * @package ESys
 */
class ESys_ValidatorRule_MaxLength extends ESys_ValidatorRule
{

    private $maxLength;

    /**
     * @param int $maxLength
     */
    public function __construct ($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (is_array($value)) {
            return count($value) <= $this->maxLength;
        }
        $value = (string) $value;
        return strlen((string) $value) <= $this->maxLength;
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_IsoDate extends ESys_ValidatorRule
{

    public function __construct ()
    {
    }

    /**
     * @todo need a better regexp
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', ((string) $value))) {
            return false;
        }
        list($year, $month, $day) = explode('-',$value);
        $parsedDate = date('Y-m-d', mktime(0,0,0,$month,$day,$year));
        return $parsedDate == $value;
    }

}


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


/**
 * @package ESys
 */
class ESys_ValidatorRule_Phone extends ESys_ValidatorRule
{

    public function __construct ()
    {
    }

    /**
     * @todo need a better regexp
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return (boolean) preg_match('/^[0-9\-. ()+]{7,}$/', $value);
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_Match extends ESys_ValidatorRule
{

    /**
     * @param string $pattern Regular expression pattern.
     */
    public function __construct ($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return (boolean) preg_match($this->pattern, $value);
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_Optional extends ESys_ValidatorRule
{

    private $validatorRule;

    /**
     * @param ESys_ValidatorRule $validatorRule
     */
    public function __construct ($validatorRule)
    {
        $this->validatorRule = $validatorRule;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (empty($value)) { return true; }
        return $this->validatorRule->validate($value);
    }

}    


/**
 * @package ESys
 */
class ESys_ValidatorRule_RepeatField extends ESys_ValidatorRule
{

    private $fieldName;

    /**
     * @param string $fieldName
     */
    public function __construct ($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        $otherData = $this->validator->getData();
        if (! isset($otherData[$this->fieldName])) { return false; }
        return ((string) $value) === ((string) $otherData[$this->fieldName]);
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_Url extends ESys_ValidatorRule
{

    private $allowedSchemes = array();

    /**
     * @param string|array $allowedSchemes
     */
    public function __construct ($allowedSchemes = array('http', 'https'))
    {
        $this->allowedSchemes = $allowedSchemes;   
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        $options = array('allowed_schemes'=>$this->allowedSchemes);
        return $this->validateUri($value, $options);
    }

    private function validateUri($url, $options = null)
    {
        $strict = ';/?:@$,';
        $domain_check = false;
        $allowed_schemes = null;
        if (is_array($options)) {
            extract($options);
        }
        if (preg_match(
             '&^(?:([a-z][-+.a-z0-9]*):)?                             # 1. scheme
              (?://                                                   # authority start
              (?:((?:%[0-9a-f]{2}|[-a-z0-9_.!~*\'();:\&=+$,])*)@)?    # 2. authority-userinfo
              (?:((?:[a-z0-9](?:[-a-z0-9]*[a-z0-9])?\.)*[a-z](?:[a-z0-9]+)?\.?)  # 3. authority-hostname OR
              |([0-9]{1,3}(?:\.[0-9]{1,3}){3}))                       # 4. authority-ipv4
              (?::([0-9]*))?)                                        # 5. authority-port
              ((?:/(?:%[0-9a-f]{2}|[-a-z0-9_.!~*\'():@\&=+$,;])*)*/?)? # 6. path
              (?:\?([^#]*))?                                          # 7. query
              (?:\#((?:%[0-9a-f]{2}|[-a-z0-9_.!~*\'();/?:@\&=+$,])*))? # 8. fragment
              $&xi', $url, $matches)) {
            $scheme = isset($matches[1]) ? $matches[1] : '';
            $authority = isset($matches[3]) ? $matches[3] : '' ;
            if (is_array($allowed_schemes) &&
                !in_array($scheme,$allowed_schemes)
            ) {
                return false;
            }
            if (!empty($matches[4])) {
                $parts = explode('.', $matches[4]);
                foreach ($parts as $part) {
                    if ($part > 255) {
                        return false;
                    }
                }
            } elseif ($domain_check && function_exists('checkdnsrr')) {
                if (!checkdnsrr($authority, 'A')) {
                    return false;
                }
            }
            if ($strict) {
                $strict = '#[' . preg_quote($strict, '#') . ']#';
                if ((!empty($matches[7]) && preg_match($strict, $matches[7]))
                 || (!empty($matches[8]) && preg_match($strict, $matches[8]))) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_Whitelist extends ESys_ValidatorRule
{

    private $whitelist;
    
    /**
     * @param array $whiteList Array of valid values.
     */
    public function __construct ($whitelist)
    {
        $this->whitelist = $whitelist;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return in_array($value, $this->whitelist);
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_Blacklist extends ESys_ValidatorRule
{

    private $blacklist;
    
    /**
     * @param array $blackList Array of invalid values.
     */
    public function __construct ($blacklist)
    {
        $this->blacklist = $blacklist;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return !in_array($value, $this->blacklist);
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_RequiredFileUpload extends ESys_ValidatorRule
{

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (! $value instanceof ESys_File_Upload) {
            return false;
        }
        return $value->submitted();
    }

}


/**
 * @package ESys
 */
class ESys_ValidatorRule_SuccessfulFileUpload extends ESys_ValidatorRule
{

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (! $value instanceof ESys_File_Upload) {
            return false;
        }
        if (! $value->submitted()) {
            return true;
        }
        return $value->transferred();
    }

}


class ESys_ValidatorRule_NotEmpty extends ESys_ValidatorRule {
	
	
	public function validate ($value)
	{
		if (is_string($value) && preg_match('/^\s+$/', $value)) {
			return false;
		}
		return strlen($value) > 0;
	}
	
	
}

