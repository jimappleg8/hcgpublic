<?php 
// *****************************************************************************
// Copyright 2003-2005 by A J Marston <http://www.tonymarston.net>
// Distributed under the GNU General Public Licence
// *****************************************************************************

class Validation {

    // member variables
    var $caller;        // details of calling object
    var $errors;        // array of error messages
    
    // ****************************************************************************
    // class constructor
    // ****************************************************************************
    function Validation ()
    {
        
    } // Validation
    
    // ****************************************************************************
    function getErrors ()
    {
        return $this->errors;
        
    } // getErrors
    
    // ****************************************************************************
    function validateEmail ($fieldname, $fieldvalue)
    // standard function for validating email addresses.
    {
        // look for 'user@hostname.domain'
        $pattern = "/"                      // start pattern
                 . "^[a-z0-9_-]+"           // valid chars (at least once)
                 . "(\.[a-z0-9_-]+)*"       // dot valid chars (0-n times)
                 . "@"                      // at
                 . "[a-z0-9][a-z0-9-]*"     // valid chars (at least once)
                 . "(\.[a-z0-9-]+)*"        // dot valid chars (0-n times)
                 . "\.([a-z]{2,6})$"        // dot valid chars
                 . "/i";                    // end pattern, case insensitive
        
        if (!preg_match($pattern, $fieldvalue)) {
            $this->errors[$fieldname] = 'Invalid format for e-mail address.';
        } // if
        
        return;
        
    } // validateEmail
    
    // ****************************************************************************
    function validateField ($fieldname, $fieldvalue, $fieldspec)
    // standard function for validating database fields
    {
        //DebugBreak();
        $dateobj = &getDateObject();

        if ($fieldspec['type'] == 'enum') {
            // get enum array for this field
            $enum = $this->caller->getValRep($fieldname); 
            // if we already have the value do not replace it
            if (!in_array($fieldvalue, $enum)) {
                // replace index number with text value
                $fieldvalue = $enum[$fieldvalue];
            } // if
        } // if
        
        // trim any leading or trailing spaces
        $fieldvalue = trim($fieldvalue);
        
        if (strlen($fieldvalue) == 0) {
            // field is empty - is it allowed to be?
            if (isset($fieldspec['required'])) {
                if (isset($fieldspec['autoinsert']) 
                 or isset($fieldspec['auto_increment'])) {
                    // value will be filled in later, so continue
                } else {
                    $this->errors[$fieldname] = "$fieldname cannot be blank";
                } // if
            } // if
            if ($fieldspec['type'] == 'date' or $fieldspec['type'] == 'datetime') {
                if (isset($fieldspec['infinityisnull'])) {
                    $fieldvalue = '9999-12-31';
                } // if
            } // if
            if ($fieldspec['type'] == 'boolean') {
                $fieldvalue = $fieldspec['false'];
            } // if
            // nothing left to validate, so return now
            return $fieldvalue;
        } // if
        
        // field is not empty - check field size
        if (isset($fieldspec['size'])) {
            $size = (int)$fieldspec['size'];
            if (strlen($fieldvalue) > $size) {
                $this->errors[$fieldname] = "$fieldname cannot be > $size characters";
            } // if
        } // if
        
        switch ($fieldspec['type']) {
            case 'boolean': 
                // result from boolean fields may be varied, so convert to TRUE or FALSE
                // (where actual values are defined within $fieldspec)
                if (is_True($fieldvalue)) {
                    $fieldvalue = $fieldspec['true'];
                } else {
                    $fieldvalue = $fieldspec['false'];
                } // if
                break;
            
            case 'string':
                if (isset($fieldspec['uppercase'])) {
                    // value in this field must be uppercase
                    $fieldvalue = strtoupper($fieldvalue);
                } // if
                if (isset($fieldspec['lowercase'])) {
                    // value in this field must be lowercase
                    $fieldvalue = strtolower($fieldvalue);
                } // if
                
                if (isset($fieldspec['subtype'])) {
                    // perform any subtype processing
                    switch ($fieldspec['subtype']) {
                        case 'filename':
                        case 'image':
                            // check that value is a valid file name
                            if (!file_exists($fieldvalue)) {
                                $this->errors[$fieldname] = 'Filename does not exist';
                            } // if
                            break;
                        case 'email':
                            // check that value is a valid email address
                            $this->validateEmail($fieldname, $fieldvalue);
                            break;
                        default:
                            $this->errors[$fieldname] = "$fieldname: specification for 'subtype' is invalid";
                    } // switch
                } // if
                
                if (isset($fieldspec['password'])) {
                    // passwords may have a 'hash' specification
                    if (isset($fieldspec['hash'])) {
                        switch ($fieldspec['hash']) {
                            case 'md5':
                                $fieldvalue = md5($fieldvalue);
                                break;
                            case 'sha1':
                                $fieldvalue = sha1($fieldvalue);
                                break;
                            case 'custom':
                                break;
                            default:
                                $this->errors[$fieldname] = "$fieldname: specification for 'hash' is invalid";
                        } // switch
                    } // if
                } // if
                
                // escape any suspect characters in string fields
                $fieldvalue = addslashes($fieldvalue);
                
                break;
            
            case 'date': 
                // value must be a date
                if (!$internaldate = $dateobj->getInternalDate($fieldvalue)) {
                    $this->errors[$fieldname] = "$fieldname: " . $dateobj->getErrors();
                } else {
                    // set date to internal format
                    $fieldvalue = $internaldate;
                } // if
                break;
            
            case 'datetime': 
                // value must be a combined date and time
                if (!$internaldatetime = $dateobj->getInternalDateTime($fieldvalue)) {
                    $this->errors[$fieldname] = "$fieldname: " . $dateobj->getErrors();
                } else {
                    // set date to internal format
                    $fieldvalue = $internaldatetime;
                } // if
                break;
            
            case 'time': 
                // value must be a time
                if (!$internaltime = $dateobj->getInternaltime($fieldvalue)) {
                    $this->errors[$fieldname] = "$fieldname: " . $dateobj->getErrors();
                } else {
                    // set time to internal format
                    $fieldvalue = $internaltime;
                } // if
                break;
            
            default: 
                // perform validation if field type is numeric (integer, decimal)
                $fieldvalue = $this->validateNumber($fieldname, $fieldvalue, $fieldspec);
        } // switch
        
        return $fieldvalue;
        
    } // validateField
    
    // ****************************************************************************
    function validateInsertArray ($fieldarray, $fieldspec, &$caller)
    // Validate contents of $fieldarray against $fieldspec array.
    // Errors are returned in $errors array.
    // NOTE: for INSERT all fields contained in $fieldspecs must be present.
    {
        //DebugBreak();
        $this->errors = array();
        
        $this->caller = &$caller;
        
        // create array to hold data which has been formatted for the database
        $insertarray = array();
        
        // step through each fieldspec entry and compare with input data
        foreach ($fieldspec as $field => $spec) {
            if (isset($fieldarray[$field])) {
                $value = $fieldarray[$field];
            } else {
                $value = null;
            } // if
            $value = $this->validateField($field, $value, $spec); 
            // transfer to array which will be passed to the database
            // (remember that a null value is not the same as no value at all)
            if (strlen($value) > 0) {
                $insertarray[$field] = $value;
            } else {
                $insertarray[$field] = null;
            } // if
        } // foreach
        
        return $insertarray;
        
    } // validateInsertArray
    
    // ****************************************************************************
    function validateNumber ($field, $value, $spec)
    // if $spec identifies $field as a number then check that $value is within range.
    {
        //DebugBreak();
        // check if field type = integer (whole numbers only)
        $pattern = '/(int1|tinyint|int2|smallint|int3|mediumint|int4|integer|int8|bigint|int)/i';
        if (preg_match($pattern, $spec['type'], $match)) {
            // test that input contains a valid value for an integer field
            $integer = (int)$value;
            if ((string)$value <> (string)$integer) {
                $this->errors[$field] = "Value is not an integer";
                return $value;
            } // if
            
            // set min/max values depending of size of field
            switch ($match[0]) {
                case 'int1':
                case 'tinyint':
                    $minvalue = -128;
                    $maxvalue = 127;
                    break;
                case 'int2':
                case 'smallint':
                    $minvalue = -32768;
                    $maxvalue = 32767;
                    break;
                case 'int3';
                case 'mediumint':
                    $minvalue = -8388608;
                    $maxvalue = 8388607;
                    break;
                case 'int':
                case 'int4':
                case 'integer':
                    $minvalue = -2147483648;
                    $maxvalue = 2147483647;
                    break;
                case 'int8':
                case 'bigint':
                    $minvalue = -9223372036854775808;
                    $maxvalue = 9223372036854775807;
                    break;
                default:
                    $this->errors[$field] = "Unknown integer type ($match)";
                    return $value;
            } // switch
            
            // adjust min/max values if integer is unsigned
            if (isset($spec['unsigned'])) {
                $minvalue = 0;
                $maxvalue = ($maxvalue * 2) + 1;
            } // if
            
            if (isset($spec['minvalue'])) {
                // override with value provided in $fieldspec
                $minvalue = (int)$spec['minvalue'];
            } // if
            
            if ($integer < $minvalue) {
                $this->errors[$field] = "Value is below minimum value ($minvalue)";
            } // if
            
            if (isset($spec['maxvalue'])) {
                // override with value provided in $fieldspec
                $maxvalue = (int)$spec['maxvalue'];
            } // if
            
            if ($integer > $maxvalue) {
                $this->errors[$field] = "Value is above maximum value ($maxvalue)";
            } // if
            
            if (isset($spec['zerofill'])) {
                while (strlen($value) < $spec['size']) {
                    $value = '0' . $value;
                } // while
            } // if
            
            return $value;
            
        } // if
        
        // check if field type = numeric (with optional decimal places)
        $pattern = '/(decimal|numeric)/i';
        if (preg_match($pattern, $spec['type'], $match)) {
            // input must at least be numeric to begin with
            if (!is_numeric(trim($value))) {
                $this->errors[$field] = "value is not numeric";
                return $value;
            } // if
            
            // value for 'precision' must be present
            if (isset($spec['precision'])) {
                $precision = (int)$spec['precision'];
            } else {
                $this->errors[$field] = "Specification missing for PRECISION";
                return $value;
            } // if
            
            // value for 'scale' is optional (default is zero)
            if (isset($spec['scale'])) {
                $scale = (int)$spec['scale'];
            } else {
                $scale = 0;
            } // if
            
            // minvalue includes negative sign
            $minvalue = '-' . str_repeat('9', $precision-1);
            
            // maxvalue has no positive sign
            $maxvalue = str_repeat('9', $precision);
            if ($scale > 0) {
                // adjust values to include decimal places
                $minvalue = $minvalue / pow(10, $scale);
                $maxvalue = $maxvalue / pow(10, $scale);
            } // if
            
            // adjust min value if value is unsigned
            if (isset($spec['unsigned'])) {
                $minvalue = 0;
            } // if
            
            if (isset($spec['minvalue'])) {
                // override with value provided in $fieldspec
                $minvalue = (float)$spec['minvalue'];
            } // if
            
            if ($value < $minvalue) {
                $this->errors[$field] = "Value is below minimum value ($minvalue)";
            } // if
            
            if (isset($spec['maxvalue'])) {
                // override with value provided in $fieldspec
                $maxvalue = (float)$spec['maxvalue'];
            } // if
            
            if ($value > $maxvalue) {
                $this->errors[$field] = "Value is above maximum value ($maxvalue)";
            } // if
            
            $value = number_format($value, $scale, '.', '');
            
            return $value;
            
        } // if
        
        return $value;
        
    } // validateNumber
    
    // ****************************************************************************
    function validateUpdateArray ($fieldarray, $fieldspec, &$caller)
    // validate contents of $fieldarray against $fieldspec array.
    // errors are returned in $errors array.
    // NOTE: for UPDATE only a subset of fields may be supplied.
    {
        $this->errors = array();
        
        $this->caller = &$caller;
        
        // create array to hold data which has been formatted for the database
        $updatearray = array();
        
        // step through input data and compare with fieldspec
        foreach ($fieldarray as $field => $value) {
            // get specifications for this field
            // (this will not carry forward any fields which do not exist in this table)
            if (array_key_exists($field, $fieldspec)) {
                $spec = $fieldspec[$field];
                
                $value = $this->validateField($field, $value, $spec); 
                // transfer to array which will be passed to the database
                // (allow null values as field may have been cleared)
                if (strlen($value) > 0) {
                    $updatearray[$field] = $value;
                } else {
                    $updatearray[$field] = null;
                } // if
            } // if
        } // foreach
        
        return $updatearray;
        
    } // validateUpdateArray
    
// ****************************************************************************
} // end class
// ****************************************************************************

?>
