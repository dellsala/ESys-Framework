<?php
/**
 * @package ESys
 */


/**
 * Class for registering validation rules, and
 * performing validation on a set of key/value pairs.
 *
 * @package ESys
 */
class ESys_Validator
{

    /**
     * The data to be validated.
     */
    private $data = array();


    /**
     * Array of validation rules.
     */
    private $rules = array();


    /**
     * Array of error codes and messages for rules that failed to validate.
     */
    private $errors = array();


    /**
     * Assign data to be validated.
     *
     * Call with one array argument to set all fields at once.
     *
     * @param array|string $value
     * @param mixed $fieldValue
     * @return void
     */
    public function setData ($value, $fieldValue = null)
    {
        if (isset($fieldValue)) {
            return $this->setDataField($value, $fieldValue);
        }
        $this->data = $value;
    }


    /**
     * @param string $key
     * @return mixed
     */
    public function getData ($key = null)
    {
        if (isset($key)) {
            return $this->getDataField($key);
        }
        return $this->data;
    }


    private function setDataField ($key, $value)
    {
        $this->data[$key] = $value;
    }


    private function getDataField ($key)
    {
        if (! array_key_exists($key, $this->data)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): the key '{$key}' ".
                'does not exist', E_USER_WARNING);
            return null;
        }
        return $this->data[$key];
    }


    /**
     * @param string $field
     * @param ESys_ValidatorRule $rule
     * @param string $message
     * @param int $code
     * @return void
     */
    public function addRule ($field, $rule, $message, $code = null)
    {
        if (isset($code) && array_key_exists($code, $this->rules)) {
            trigger_error("ESys_Validator::addRule(): a rule with code '$code' ".
                'has already been added', E_USER_WARNING);
            return false;
        }
        if (! $rule instanceof ESys_ValidatorRule) {
            trigger_error("ESys_Validator::addRule(): rule '.
                'is not a valid ESys_ValidatorRule object", E_USER_ERROR);
            return false;
        }
        $rule->setValidator($this);
        $ruleRecord = array(
            'field' => $field,
            'rule' => $rule,
            'message' => $message
        );
        if (isset($code)) {
            $this->rules[$code] = $ruleRecord;
        } else {
            $this->rules[] = $ruleRecord;
        }
        return true;
    }


    /**
     * @return array
     */
    public function getErrors ()
    {
        return $this->errors;
    }



    /**
     * @return ESys_Validator_ErrorReport
     */
    public function getErrorReport ()
    {
        return new ESys_Validator_ErrorReport($this->errors);
    }


    /**
     * @return boolean
     */
    public function validate ()
    {
        $isValid = true;
        foreach ($this->rules as $code => $ruleRecord) {
            $rule = $ruleRecord['rule'];
            $message = $ruleRecord['message'];
            $field = $ruleRecord['field'];
            if (! array_key_exists($field, $this->data)) {
                trigger_error('ESys_Validator::validate(): the field '.$field.' '.
                    'is not available', E_USER_WARNING);
                $isValid = false;
                continue;
            }
            if (! $rule->validate($this->data[$field])) {
                $isValid = false;
                $this->errors[$field][] = array(
                    'code' => $code,
                    'message' => $message
                );
            }
        }
        return $isValid;
    }


}



/**
 * Wraper class for ESys_Validator error data
 *
 * @package ESys
 */
class ESys_Validator_ErrorReport {


    /**
     * The raw error data
     */
    private $errorData = array();

    /**
     * The fields with errors
     */
    private $errorFields;

    /**
     * The error messages
     */
    private $errorMessages;


    /**
     * The error codes
     */
    private $errorCodes;


    /**
     * @param array $errorData
     */
    public function __construct ($errorData)
    {
        $this->errorData = $errorData;
    }
    

    /**
     * @return int
     */
    public function errorCount ()
    {
        return count($this->getCodes());
    }


    /**
     * @return array
     */
    public function getCodes ()
    {
        if (! count($this->errorData)) {
            return array();
        }
        if (! $this->errorCodes) {
            $this->errorCodes = array();
            foreach ($this->errorData as $field => $errorList) {
                foreach ($errorList as $error) {
                    $this->errorCodes[] = $error['code'];
                }
            }
        }
        return $this->errorCodes;
    }


    /**
     * @param string fieldName
     * @return array
     */
    public function getMessages ($fieldName = null)
    {
        if (! count($this->errorData)) {
            return array();
        }
        if (isset($fieldName)) {
            $messages = array();
            if (! isset($this->errorData[$fieldName])) {
                return array();
            }
            $errorList = $this->errorData[$fieldName];
            foreach ($errorList as $error) {
                $messages[] = $error['message'];
            }
            return $messages;
        }
        if (! $this->errorMessages) {
            $this->errorMessages = array();
            foreach ($this->errorData as $field => $errorList) {
                foreach ($errorList as $error) {
                    $this->errorMessages[] = $error['message'];
                }
            }
        }
        return $this->errorMessages;
    }


    /**
     * @return array
     */
    public function getFields ()
    {
        if (! count($this->errorData)) {
            return array();
        }
        if (! $this->errorFields) {
            $this->errorFields = array_keys($this->errorData);
        }
        return $this->errorFields;
    }


    /**
     * @return array
     */
    public function getRawData ()
    {
        return $this->errorData;
    }


}

