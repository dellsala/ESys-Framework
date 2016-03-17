<?php
/**
 * @package ESys
 */


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

