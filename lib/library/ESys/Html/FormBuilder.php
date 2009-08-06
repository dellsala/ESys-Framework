<?php

/**
 * @package ESys
 */
class ESys_Html_FormBuilder
{

    private $autoEcho = true;

    private $templates = array(
        'text' => '<input type="text" name="%s" value="%s"%s>',
        'textarea' => '<textarea name="%s"%s>%s</textarea>',
        'hidden' => '<input type="hidden" name="%s" value="%s"%s>',
        'checkbox' => '<input type="checkbox" name="%s" value="%s"%s>',
        'radio' => '<input type="radio" name="%s" value="%s"%s>',
        'selectstart' => '<select name="%s"%s>',
        'option' => '<option value="%s"%s>%s</option>',
        'selectend' => '</select>',
        'password' => '<input type="password" name="%s" value=""%s>',
        'file' => '<input type="file" name="%s"%s>',
        'submit' => '<input type="submit" name="%s" value="%s"%s>',
    );

    private $errorFields = array();
    private $errorClassName = 'error';


    /**
     * @param array $data
     */
    public function __construct ($data = null)
    {
        $this->data = $data;
    }

    

    protected function getDataValue ($name, $default = '')
    {
        $nameParts = explode('[', $name);
        if (count($nameParts) == 2 && substr($name, -1) == ']') {
            $nameSet = $nameParts[0];
            $nameKey = substr($nameParts[1], 0, -1);
            if (isset($this->data[$nameSet][$nameKey])) {
                return $this->data[$nameSet][$nameKey];
            }
        }
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }


    /**
     * @param boolean $value
     * @return void
     */
    public function setAutoEcho ($value)
    {
        $this->autoEcho = $value;
    }
    

    /**
     * @param array $fieldList
     * @param string $className
     * @return void
     */
    public function flagErrors ($fieldList, $className = null)
    {
        $this->errorFields = $fieldList;
        if (isset($className)) {
            $this->errorClassName = $className;
        }
    }


    /**
     * @param string $name
     * @param array $attributes
     * @return string|void
     */
    public function input ($name, $attributes = null)
    {
        return $this->_buildBasicElement('text', $name, $attributes);
    }


    /**
     * @param string $name
     * @param array $attributes
     * @return string|void
     */
    public function hidden ($name, $attributes = null)
    {
        return $this->_buildBasicElement('hidden', $name, $attributes);
    }


    /**
     * @param string $name
     * @param array $attributes
     * @return string|void
     */
    public function password ($name, $attributes = null)
    {
        $this->_applyErrorFlag($name, $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $html = sprintf($this->templates['password'], $name, $attributes);
        return $this->_render($html);
    }


    /**
     * @param string $name
     * @param array $attributes
     * @return string|void
     */
    public function file ($name, $attributes = null)
    {
        $this->_applyErrorFlag($name, $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $html = sprintf($this->templates['file'], $name, $attributes);
        return $this->_render($html);
    }

    /**
     * @param string $name
     * @param array $attributes
     * @return string|void
     */
    public function textarea ($name, $attributes = null)
    {
        $value = $this->getDataValue($name, '');
        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        $this->_applyErrorFlag($name, $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $html = sprintf($this->templates['textarea'], $name, $attributes, $value);
        return $this->_render($html);
    }


    /**
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @return string|void
     */
    public function select ($name, $options, $attributes = null)
    {
        $this->_applyErrorFlag($name, $attributes);
        $isMultiple = is_array($attributes) && array_key_exists('multiple', $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $formName = $isMultiple ? $name.'[]' : $name;
        $html = sprintf($this->templates['selectstart'], $formName, $attributes);
        foreach ($options as $optionLabel => $optionValue) {
            $optionAttributes = array();
            $selectedValues = $this->getDataValue($name, array());
            if (! is_array($selectedValues)) {
                $selectedValues = array($selectedValues);
            }
            if (in_array($optionValue, $selectedValues)) {
                $optionAttributes['selected'] = 'true';
            }
            $optionAttributes = $this->_stringifyAttributes($optionAttributes);
            $html .= "\n".sprintf($this->templates['option'],
                htmlentities($optionValue, ENT_COMPAT, 'UTF-8'), $optionAttributes, htmlentities($optionLabel, ENT_COMPAT, 'UTF-8'));
        }
        $html .= "\n".$this->templates['selectend'];
        return $this->_render($html);
    }


    /**
     * @param string $name
     * @param array $attributes
     * @return string|void
     */
    public function submit ($name, $attributes = null)
    {
        return $this->_buildBasicElement('submit', $name, $attributes);
    }


    /**
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return string|void
     */
    public function checkbox ($name, $value, $attributes = null)
    {
        if (! is_array($attributes)) {
            $attributes = array();
        }
        if ($this->getDataValue($name, null) == $value) {
            $attributes['checked'] = true;
        }
        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        $this->_applyErrorFlag($name, $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $html = sprintf($this->templates['checkbox'], $name, $value, $attributes);
        return $this->_render($html);
    }


    /**
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return string|void
     */
    public function radio ($name, $value, $attributes = null)
    {
        if (! is_array($attributes)) {
            $attributes = array();
        }
        $selectedValue = $this->getDataValue($name, null);
        if ($selectedValue == $value) {
            $attributes['checked'] = 'true';
        }
        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        $this->_applyErrorFlag($name, $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $html = sprintf($this->templates['radio'], $name, $value, $attributes);
        return $this->_render($html);
    }


    private function _buildBasicElement ($type, $name, $attributes)
    {
        $value = $this->getDataValue($name, '');
        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        $this->_applyErrorFlag($name, $attributes);
        $attributes = $this->_stringifyAttributes($attributes);
        $html = sprintf($this->templates[$type], $name, $value, $attributes);
        //$html = str_replace(' name=""', '', $html);
        return $this->_render($html);
    }


    private function _stringifyAttributes ($attributes)
    {
        if (is_null($attributes)) { return ''; }
        if (! is_array($attributes)) { 
            trigger_error(__CLASS__.'::'.__FUNCTION__.'() attributes argument isn\'t an array',
                E_USER_WARNING);
            return '';
        }
        $attributeString = '';
        foreach ($attributes as $key => $val) {
            $key = (string) $key;
            $val = (string) $val;
            if (! preg_match('/^[A-Za-z_]\w+$/', $key)) {
                trigger_error('ESys_Html_FormBuilder::_stringifyAttributes(): '.
                    'invalid attribute name. skipping attribute', E_USER_NOTICE);
                continue;
            }
            switch ($key) {
                case 'checked':
                case 'disabled':
                case 'multiple':
                case 'selected':
                    if (! $val) {
                        continue;
                    }
                    $attributeString .= ' '.$key;
                    break;
                default:
                    $attributeString .= ' '.$key.'="'.htmlentities($val, ENT_COMPAT, 'UTF-8').'"';
                    break;
            }
        }
        return $attributeString;
    }


    private function _applyErrorFlag ($name, &$attributes)
    {
        if (! in_array($name, $this->errorFields)) { return; }
        $attributes['class'] = isset($attributes['class'])
            ? $attributes['class'].' '.$this->errorClassName
            : $this->errorClassName;
    }


    private function _render ($string)
    {
        if (! $this->autoEcho) {
            return $string;
        }
        echo $string;
        return null;
    }

}
