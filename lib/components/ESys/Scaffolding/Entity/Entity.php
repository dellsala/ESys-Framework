<?php

class ESys_Scaffolding_Entity_Entity {


	private $table;
	private $packageName;


	public function __construct (ESys_DB_Reflector_Table $table, $packageName)
	{
		$this->table = $table;
		$this->packageName = $packageName;
	}


	public function tableName ()
	{
		return $this->table->getName();
	}


	public function fileName ()
	{
	    return str_replace('_', '/', $this->className()).'.php';
	}


	public function packageName ()
	{
	    return $this->packageName;
	}

	public function className ()
	{
		return $this->packageName.'_'.ucfirst($this->instanceName());
	}


	public function instanceName ()
	{
		$nameParts = explode('_', $this->table->getName());
		array_walk($nameParts, create_function('&$string', '$string = ucfirst($string);'));
		$nameParts[0] = strtolower($nameParts[0]);
		return implode('', $nameParts);
	}


	public function displayName ()
	{
		$nameParts = explode('_', $this->table->getName());
		array_walk($nameParts, create_function('&$string', '$string = ucfirst($string);'));
		return implode(' ', $nameParts);
	}


	public function primaryAttribute ()
	{
		$columnList = $this->table->fetchColumns();
		$indexedColumnList = array();
		foreach ($columnList as $column) {
			$indexedColumnList[$column->getName()] = clone $column;
		}
		$primaryFieldNameList = array(
		    'name',
		    'title',
		    'description',
		);
		foreach ($primaryFieldNameList as $fieldName) {
            if (array_key_exists($fieldName, $indexedColumnList)) {
                return new ESys_Scaffolding_Entity_Entity_Attribute($indexedColumnList[$fieldName]);
            }
        }
		foreach ($indexedColumnList as $column) {
			if ($column->getType() == 'VARCHAR') {
				return new ESys_Scaffolding_Entity_Entity_Attribute($column);
			}
		}
		return new ESys_Scaffolding_Entity_Entity_Attribute($indexedColumnList['id']);
	}


	public function attributeList ()
	{
		$columnList = $this->table->fetchColumns();
		$attributeList = array();
		foreach ($columnList as $column) {
			if ($column->getType() != 'TIMESTAMP') {
				$attributeList[] = new ESys_Scaffolding_Entity_Entity_Attribute($column);
			}
		}
		return $attributeList;
	}

}



class ESys_Scaffolding_Entity_Entity_Attribute {


	private $column;


	public function __construct (ESys_DB_Reflector_Column $column)
	{
		$this->column = $column;
	}


	public function name ()
	{
		return $this->column->getName();
	}


	public function type ()
	{
		return $this->column->getType();
	}


	public function typeInfo ()
	{
		return $this->column->getTypeInfo();
	}


	public function defaultValue ()
	{
		return $this->column->getDefault();
	}


    public function isBoolean ()
    {
        $typeInfo = $this->typeInfo();
        $enumValues = $typeInfo['spec'];
        return $enumValues == "'Y','N'";
    }


	public function displayName ()
	{
		$nameParts = explode('_', $this->column->getName());
		array_walk($nameParts, create_function('&$string', '$string = ucfirst($string);'));
		return implode(' ', $nameParts);
	}


	public function ruleFragments () 
	{
		$validationData = array();
		$typeInfo = $this->column->getTypeInfo();
		$messageFieldName = 'The '.str_replace('_', ' ', $this->column->getName()).' field ';
		switch ($typeInfo['name']) {
			case ('VARCHAR') :
			case ('CHAR') :
				$maxLength = $typeInfo['spec'];
				$validationData[] = array(
					'ruleFragment'	=> 'new ESys_ValidatorRule_MaxLength('.$maxLength.')',
					'message'		=> $messageFieldName.'must less than '.($maxLength + 1).' characters long.',
				);
				if (stripos('email', $this->column->getName()) !== false) {
					$emailRule = 'new ESys_ValidatorRule_Email(true)';
					if ($this->column->isNull()) {
						$emailRule = 'new ESys_ValidatorRule_Optional('.$emailRule.')';
					}
					$validationData[] = array(
						'ruleFragment'	=> $emailRule,
						'message'		=> $messageFieldName.'must be a valid email address.',
					);
				}
				if (stripos('url', $this->column->getName()) !== false) {
					$urlRule = 'new ESys_ValidatorRule_Url()';
					if ($this->column->isNull()) {
						$urlRule = 'new ESys_ValidatorRule_Optional('.$urlRule.')';
					}
					$validationData[] = array(
						'ruleFragment'	=> $urlRule,
						'message'		=> $messageFieldName.'must be a valid URL.',
					);
				}
				if (stripos('phone', $this->column->getName()) !== false) {
					$phoneRule = 'new ESys_ValidatorRule_Phone()';
					if ($this->column->isNull()) {
						$phoneRule = 'new ESys_ValidatorRule_Optional('.$phoneRule.')';
					}
					$validationData[] = array(
						'ruleFragment'	=> $phoneRule,
						'message'		=> $messageFieldName.'must be a valid phone number.',
					);
				}
				if (! $this->column->isNull()) {
					$validationData[] = array(
						'ruleFragment'	=> 'new ESys_ValidatorRule_MinLength(1)',
						'message'		=> $messageFieldName.'cannot be empty.',
					);
				}
			break;
			case ('DATE') :
				$rule = 'new ESys_ValidatorRule_IsoDate()';
				if ($this->column->isNull()) {
					$rule = 'new ESys_ValidatorRule_Optional('.$rule.')';
				}
				$validationData[] = array(
					'ruleFragment'	=> $rule,
					'message'		=> $messageFieldName.'must be a valid date in ISO format (YYYY-MM-DD).',
				);
			break;
			case ('ENUM') :
				$enumValues = $typeInfo['spec'];
				$enumValues = str_replace("''", "\'", $enumValues);
				$whitelistLiteral = 'array('.$enumValues.')';
				$enumValues = str_replace("\'", "'", $enumValues);
				$enumRule = 'new ESys_ValidatorRule_Whitelist('.$whitelistLiteral.')';
				if ($this->column->isNull()) {
					$enumRule = 'new ESys_ValidatorRule_Optional('.$enumRule.')';
				}
				$validationData[] = array(
					'ruleFragment'	=> $enumRule,
					'message'		=> $messageFieldName.'must be one of the following values: '.
									   $enumValues.'.',
				);
			break;
			default:
				if (! $this->column->isNull() && $this->column->getName() != 'id') {
					$validationData[] = array(
						'ruleFragment'	=> 'new ESys_ValidatorRule_MinLength(1)',
						'message'		=> $messageFieldName.'cannot be empty.',
					);
				}
			break;
		}
		return $validationData;
	}


}


