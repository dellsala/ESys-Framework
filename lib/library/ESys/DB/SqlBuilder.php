<?php


/**
 * @package ESys
 */
class ESys_DB_SqlBuilder {


    private $db;


    /**
     * @param ESys_DB_Connection $dbConnection
     */
    public function ESys_DB_SqlBuilder ($dbConnection)
    {
        $this->db = $dbConnection;
    }


    /**
     * @param string $table
     * @param array $rowData
     * @return string
     */
    public function buildInsert ($table, $rowData)
    {
        $sql = "INSERT INTO `{$table}`\n";
        $fieldNames = array_map(
            create_function('$v', 'return \'`\'.$v.\'`\';'),
            array_keys($rowData)
        );
        $sql .= "(" . implode(", ", $fieldNames) . ")\n";
        $sqlValues = $this->convertValue(array_values($rowData));
        $sql .= "VALUES (" . implode(", ", $sqlValues) . ")\n";
        return $sql;
    }


    /**
     * @param string $table
     * @param array $rowData
     * @param string $primaryKey
     * @return string
     */
    public function buildUpdate ($table, $rowData, $primaryKey = 'id')
    {
        if (! array_key_exists($primaryKey, $rowData)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                "Primary key field '{$primaryKey}' could not be found in row data.", 
                E_USER_WARNING);
            return false;
        }
        $primaryKeyValue = $this->convertValue($rowData[$primaryKey]);
        unset($rowData[$primaryKey]);
        $sql = "UPDATE `{$table}`\n";
        $sqlSetValues = array();
        foreach ($rowData as $key => $value) {
            $sqlSetValues[] = "`{$key}` = ".$this->convertValue($value);
        }
        $sql .= "SET " . implode(",\n", $sqlSetValues) . "\n";
        $sql .= "WHERE `{$primaryKey}` = {$primaryKeyValue}";
        return $sql;
    }




    /**
     * Convert a php value to an SQL literal.
     *
     * Guesses intended sql literal type by looking at PHP value type and
     * value content. PHP null is converted to an SQL null. Performs
     * escapping on all strings.
     *
     * @param  mixed   $value       Any arbirary value or array of values.
     * @param  string  $forcedType  Force conversion to a specific SQL type. (STRING|NUMERIC|NULL)
     * @return string  An SQL literal.
     */
    public function convertValue ($value, $forcedType = null)
    {
        if (is_array($value)) {
            $convertedValue = array();
            foreach ($value as $key => $val) {
                $convertedValue[$key] = $this->convertValue($val);
            }
            return $convertedValue;
        }
        $type = null;
        if (isset($forcedType)) {
            $type = $forcedType;
        } else if (! isset($value)) {
            $type = 'NULL';
        } else if (is_int($value)
            || (ctype_digit((string) $value) && substr($value, 0, 1) !== '0'))
        {
            $type = 'NUMERIC';
        } else  {
            $type = 'STRING';
        }
        if (! in_array($type, array('NULL', 'NUMERIC', 'STRING'))) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                'Invalid SQL type. Using STRING type instead.', E_USER_WARNING);
            $type = 'STRING';            
        }
        switch ($type) {
            case 'NULL':
                $convertedValue = empty($value) ? "NULL" : "NOT NULL";
                break;
            case 'NUMERIC':
                $convertedValue = (string) $value;
                break;
            case 'STRING':
            default:
                $convertedValue = "'".$this->db->escape($value)."'";
                break;
        }
        return $convertedValue;
    }


}