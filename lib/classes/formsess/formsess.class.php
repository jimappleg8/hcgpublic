<?php
/**
 * Forms error handling class, heavily based on sessions.
 *
 * Allows basic checkings on forms, and sets session variables according to the results.
 *
 * @package formsess
 * @author Katana <katana@katana-inc.com>
 * @package formsess
 * @version $Id: formsess.class.php,v 1.10 2003/08/12 12:44:01 katana Exp $
 */
class formsess {
  
  /**
   * Variables
   */
  var $_form;                              // form name
  var $_source;                            // Data source (_POST, _GET_, _REQUEST, ...)
  var $_sess_space;                        // Storage space for the form errors and values
  var $_valid = true;                      // Last validation call result
  var $_select_stack = array();            // Current select item array
  var $current_select = '';                // reference to the current select item
  var $_current_select_ismultiple = false; // indicates if the current select is multiple (CQFD => WHTBS)
  var $_fieldkeys = array();               // cache for the field names so that we don't have to preg everytime
  var $_fieldvalues = array();             // cache for the field values so that we don't have to read everytime
  var $_magic_quotes_gpc;                  // Magic quotes GPC setting, loaded on startup
  var $_field_types = array();             // Defined types for fields
  var $_fs_root;                           // Formsess' root folder
  var $_timeout = 0;                       // timeout for the form data
    
  /**
   * Valid field types (@see setType, _set_field_type, getType, _get_field_type)
   **/
  var $_valid_field_types = array('text', 'password', 'input_file', 'textarea', 'radio', 'checkbox', 'select', 'select_multiple', 'hidden', 'date', 'time');

  
  /**
   * Constructor: sets the data source, initializes the data container
   *
   * @param String $form form name
   * @param String $method form method, POST or GET
   * @return void
   */
  function formsess($form, $method = 'POST') {
    $this->_fs_root = dirname(__FILE__);

    // used to access the fs object from any place
    $this->_form = $form;
    $GLOBALS["__fs_$form"] = &$this;

    // check the source
    if (strtoupper($method) == 'POST') {
      $this->_source = &$_POST;
    } elseif (strtoupper($method) == 'GET') {
      $this->_source = &$_GET;
    } else {
      return false;
    }

    // Initializes the initial data container
    if (!isset($_SESSION['fs_container']) || !is_array($_SESSION['fs_container'])) $_SESSION['fs_container'] = array();

    // Session state not created, create now. Cleanup the stuff a little first
    if (!isset($_SESSION['fs_container'][$form]) || !is_array($_SESSION['fs_container'][$form])) {
      $_SESSION['fs_container'][$form] = array();
      $new = true; // used to check if we have to create the arrays first
    } else {
      $new = false;
    }

    $this->_sess_space = &$_SESSION['fs_container'][$form];

    $this->_fieldvalues = array();
    $this->_fieldkeys = array();

    $this->_magic_quotes_gpc = get_magic_quotes_gpc();

    // will initialise the session space so that it can store the needed data
    if ($new) $this->reset();

    return;
  }


  /**
   * Runs a validation callback on one or more fields.
   *
   * @param string $check test to be run
   * @param string $fields Field to test. Can also be an array if the check implies several fields
   * @param array $params Test parameters
   * @param string $message Error message to use if the check fails (will overwrite the default message)
   * @return void
   */
  function check($check, $fields, $params = 0, $message = false) {

    // Just save the field
    if ($check == 'save') {
      $this->_valid = true;
    } else {
      // the validator has not been included yet
      $function = "fs_validation_callback_$check";

      if (!function_exists($function)) {
        $file = $this->_fs_root . '/validators/' . strtolower($check) . '.php';

        if (!file_exists($file)) {
          $this->_trigger_error("[FSValidator:size] file $file not found");

          return false;
        } else {
          ini_set('track_errors', 1);
          if (!@include $file) {
            $this->_trigger_error("error in $file: $php_errormsg"); 
            return false;
          } elseif (!function_exists($function)) {
            $this->_trigger_error("function $function not found in $file\n");
            return false;
          }
        }
      }
      // transform the list of fieldnames in a list of fieldnames => fieldvalues
      $f = array();

      if (!is_array($fields)) settype($fields, 'array');

      foreach ($fields as $fieldname) {
        $f[] = array('name' => $fieldname, 'value' => $this->_read_field_value($fieldname));
      }
      // ok, run the callback
      $function($f, $params, $this, $message);
    }
    // Save the valid values
    if ($this->_valid && (!$res = $this->_field_has_errors($fields))) {
      $this->_save_fields($fields);
    } else {
      $this->_unsave_fields($fields);
    }

    return;
  }


  /**
   * Manually sets an error on a field
   *
   * Also deletes the saved value for the triggered field, unless specified not to do so.
   *
   * @param String $field Field to set the error for
   * @param String $error_message Error message
   * @param Integer $error_code error code
   * @return void
   */
  function trigger_error($field, $error_message, $error_code = 0) {
    $this->_sess_space['errors'][$field][] = array('code' => $error_code, 'message' => $error_message);
    unset($this->_sess_space['values'][$field]);

    return;
  }


  /**
   * Deletes the form save state
   *
   * @param string $target what has to be deleted
   *      - values: stored values
   *      - errors: errors
   *      - types:  Field types
   *      - all:    values + errors + types
   * @return void
   */
 function reset($target = 'all') {
    switch ($target) {

      // all
      case 'all':
        $this->_sess_space = array(
          'values' => array(),
          'errors' => array(),
          'types'  => array(),
          'checks' => array()
        );
        break;
        
      // the values
      case 'values':
        $this->_sess_space['values'] = array();
        break;


      // the errors
      case 'errors':
        $this->_sess_space['errors'] = array();
        break;

      // validations
      case 'checks':
        $this->_sess_space['checks'] = array();
        break;

      // the fields types
      case 'types':
        $this->_sess_space['types'] = array();
        break;
    }
    return;
  }

  /**
   * Tells if there were errors so far in the form checking
   *
   * @return bool true if errors were registered, false otherwise
   */
  function error() {
    return count($this->_sess_space['errors']) ? true : false;
  }

  /**
   * Alias for error() (@see error())
   * 
   * @return bool 
   **/
  function hasErrors() {
    return $this->error();
  }
  

  /**
   * Tells if data was saved from the form
   *
   * @return bool true if data was saved, false otherwise
   */
  function saved_data() {
    return count($this->_sess_space['values']) ? true : false;
  }


  /**
   * Returns the stored value for a specific field
   *
   * @return string
   */
  function get_value($field) {
    if (isset($this->_sess_space['values'][$field])) {
      return $this->_sess_space['values'][$field];
    } else {
      return false;
    }
  }


  /**
   * Returns the errors list
   * @param $field string field to return the errors for. If no field is specified all the errors are returned.
   *
   * @return array An associative array with the errors
   */
  function get_errors($field = '') {
    
    // we don't need the [] to get a message
    $field = str_replace('[]', '', $field); 

    if (empty($field)) { // no specific field, all errors
      return $this->_sess_space['errors'];
    }
    elseif (!isset($this->_sess_space['errors'][$field])) { // requested field does not exist
      return false;
    }
    else { // ok, field found
      return $this->_sess_space['errors'][$field];
    }
  }


  /**
   * saves the value of a given form field in the session
   *
   * @param String $field field to be saved. Can also be an array of field names
   * @access private
   * @return void
   */
  function _save_fields($fields) {
    if (!is_array($fields)) settype($fields, 'array');
    foreach ($fields as $field) {
      $this->_sess_space['values'][$field] = $this->_read_field_value($field);
    }
    return;
  }


  /**
   * Delete saved values
   *
   * @param String $field field to be unsaved. Can also be an array of field names
   * @access private
   * @return void
   */
  function _unsave_fields($fields) {
    if (!is_array($fields)) settype($fields, 'array');
    foreach ($fields as $field) {
      if (isset($this->_sess_space['values'][$field])) {
        unset($this->_sess_space['values'][$field]);
      }
      if (isset($this->_fieldvalues[$field])) {
        unset($this->_fieldvalues[$field]);
      }
      if (isset($this->_sess_space['types'][$field])) {
        unset($this->_sess_space['types'][$field]);
      }
      if (isset($this->_fieldkeys[$field])) {
        unset($this->_fieldkeys[$field]);
      }
    }

    return;
  }


  /**
   * Public wrapper to the private method _unsave_fields. Deletes a saved value
   *
   * @param string $field field to delete
   * @return void
   */
  function forget_field($field) {
    $this->_unsave_fields($field);
  }


  /**
   * Checks if a field is set in the data source
   *
   * @param  $field field to check
   * @return bool true if the field is set, or false
   */
  function _field_exists($field) {
    $realkey = $this->_parse_field_name($field);

    $source = $this->_get_field_source($field);
        
    // array item
    if (is_array($realkey)) {
      $curvar = &$this->_source;

      foreach ($realkey as $key) {
        if (!isset($curvar[$key])) {
          return false;
        } else {
          $curvar = &$curvar[$key];
        }

        return true;
      }
      // simple key
    } else {
      return array_key_exists($field, $this->_source) ? true : false;
    }
  }


  /**
   * Checks if an error was found on a field
   *
   * @param string $fields field name
   * @return bool true if there was an error, or false
   */
  function _field_has_errors($fields) {
    if (!is_array($fields)) settype($fields, 'array');
    foreach ($fields as $field) {
      if (isset($this->_sess_space['errors'][$field])) {
        return true;
      }
    }

    return false;
  }


  /**
   * Deletes all that is stored (and the storage space !)
   *
   * @return void
   */
  function destroy() {
    unset($this->_sess_space);
    return;
  }


  /**
   * Assigns a value to a field.
   *
   * If a value was already assigned, it is not overwritten by default. Use $force = true to do this.
   *
   * @param string $field field's name
   * @param string $value value to assign
   * @param bool $force overwrite even if a value is already set
   */
  function assign($field, $value, $force = false) {
    // If overwrite was not asked, we test if a value exists
    if (!$force && (isset($this->_sess_space['values'][$field]) && !empty($this->_sess_space['values'][$field]))) {
      return;
    }

    // a date field is a particular case
    if ($this->get_type($field) == 'date') {
    	// If $time is not in format yyyy-mm-dd
    	if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
    		if (!function_exists('smarty_make_timestamp')) {
    		  require SMARTY_DIR . 'plugins' . DIRECTORY_SEPARATOR . 'shared.make_timestamp.php';
    		}
    		$value = strftime('%Y-%m-%d', smarty_make_timestamp($value));
    	}
      list($year, $month, $day) = explode('-', $value);
      $this->assign($field . 'Year',  $year,  $force);
      $this->assign($field . 'Month', $month, $force);
      $this->assign($field . 'Day',   $day,   $force);
    }
    // Assign to the source & save
    else {
      $this->_source[$field] = $value;
    }
    
    $this->_save_fields($field);
  }


  /**
   * Saves the value in a field
   * Wrapper to formsess::check('save', $field)
   *
   * @param string $field field to save
   * @param string $fieldN other fields to save
   * @return void
   */
  function save($field) {
    
    for ($i = 0, $count = func_num_args(); $i < $count; $i++) {
      $param = func_get_arg($i);
      $this->check('save', $param);
    }

    return;
  }


  /**
   * Reads a field value
   *
   * @param string $field
   * @return mixed the field value (string or array)
   */
  function _read_field_value($field) {

    // first check within the cached values so that we don't read twice
    if (isset($this->_fieldvalues[$field])) {
      return $this->_fieldvalues[$field];
    }
    
    // if this field was not read before, read it from the source
    else {
      $source = $this->_get_field_source($field);
      $realkey = $this->_parse_field_name($field);

      // read the value for the field
      // the method is different if the field is a date field
      if ($this->get_type($field) == 'date') {
        $curvar = sprintf('%4d-%02d-%02d', $this->_read_field_value($field . 'Year'), $this->_read_field_value($field . 'Month'), $this->_read_field_value($field . 'Day'));
      }
      elseif ($this->get_type($field) == 'time') {
        $curvar = sprintf('%02d:%02d:%02d', $this->_read_field_value($field . 'Hour'), $this->_read_field_value($field . 'Minute'), $this->_read_field_value($field . 'Second'));
      }
      else { // if ($this->getFieldType($field) == 'date') {
        if (is_array($realkey)) {
          $curvar = &$source;
  
          foreach ($realkey as $key) {
            if (!isset($curvar[$key])) return false;
            $curvar = &$curvar[$key];
          }
        } elseif (!isset($source[$realkey])) {
          return false;
        } else {
          $curvar = &$source[$realkey];
        }
      }

      // strip slashes if needed, then cache the values
      if ($this->_magic_quotes_gpc && ($this->get_type($field) != 'input_file')) {
        
        // if it's an array, stripslash each value in the array
        if (is_array($curvar)) {
          foreach($curvar as $key => $var) {
            $curvar[$key] = stripslashes($var);
          }

          $this->_fieldvalues[$field] = $curvar;
          // just one single value, no array
        } elseif (is_string($curvar) or is_numeric($curvar)) {
          $this->_fieldvalues[$field] = stripslashes($curvar);
        } else {
          $this->_fieldvalues[$field] = '';
        }
      } else {
        $this->_fieldvalues[$field] = $curvar;
      }

      return $this->_fieldvalues[$field];
    }
  }

  /**
   * Parses a field name to get the different parts and read as an array if necessary
   *
   * @param string $fieldname complete field name
   * @return mixed string or array
   */
  function _parse_field_name($fieldname) {

    // we first attempt to get the field keys from the cache
    if (isset($this->_fieldkeys[$fieldname])) {
      return $this->_fieldkeys[$fieldname];
    }
    // not cached, parse
    else {
      // 1st check for the par outside brackets, at the beggining
      if (preg_match('/^( [^\[]* ) \[ (.*) \] $/x', $fieldname, $m)) {
        $keys = array();
        $keys[] = $m[1];
        $rest = explode('][', $m[2]);

        for ($i = 0, $count = count($rest); $i < $count; $i++) {
          if ($rest[$i] != '') $keys[] = $rest[$i];
        }

        $this->_fieldkeys[$fieldname] = $keys;

        return $keys;
      } else {
        $this->_fieldkeys[$fieldname] = $fieldname;

        return $fieldname;
      }
    }
  }


  /**
   * Sets a field type
   *
   * @param string $field field name
   * @param string $type  field type (must be a valid one)
   * @return void
   * @deprecated use get_type
   */
  function setType($field, $type) {
    return $this->_set_field_type($field, $type);
  }
  

  /**
   * Sets a field type
   *
   * @param string $field field name
   * @param string $type  field type (must be a valid one)
   * @return void
   */
  function set_type($field, $type) {
    return $this->_set_field_type($field, $type);
  }
  

  /**
   * Returns the field type for a field
   * 
   * @param string $field the field name
   * @return string the field type, or false if it's not set
   * @deprecated use get_type
   **/
  function getType($field) {
    return $this->_get_field_type($field); 
  }
  

  /**
   * Returns the field type for a field
   * 
   * @param string $field the field name
   * @return string the field type, or false if it's not set
   **/
  function get_type($field) {
    return $this->_get_field_type($field); 
  }
  

  /**
   * Returns a reference to the data storage area for the given field
   * (according to the file type)
   * 
   * @return &array reference to $this->_source or $_FILES 
   **/
  function &_get_field_source($field) {

    // change the data source if we are working on a file
    if ($this->get_type($field) == 'input_file') {
      return $_FILES;
    } else {
      return $this->_source;
    }
  }
  

  /**
   * Defines a field type and stores it into the session space
   * 
   * @param string $field field name
   * @param string $type  field type (text, password, textarea, radio, checkbox, select, select_multiple, file)
   * @return void
   * @access private
   **/
  function _set_field_type($field, $type) {
    if (!in_array($type, $this->_valid_field_types)) {
      return false;
    }
    
    else {
      $this->_sess_space['types'][$field] = $type;
      return;      
    }
  }
  

  /**
   * Returns the type of a field
   * 
   * @param string $field the field name
   * @return string the field type, or false
   **/
  function _get_field_type($field) {
    return !isset($this->_sess_space['types'][$field]) ? false : $this->_sess_space['types'][$field]; 
  }

  /**
   * Triggers an error during formsess run
   * 
   * @param string $message Message d'erreur à afficher
   * @param int    $error Error type (E_USER_NOTICE, E_USER_WARNING, E_USER_ERROR)
   **/
  function _trigger_error($message, $error = false) {
    if (!$error) $error = E_USER_WARNING;
    trigger_error($message, $error);
  }
  
  /**
   * Adds a validation method to call upon performCheck()
   * 
   * @param string $check test to be run
   * @param string $fields Field to test. Can also be an array if the check implies several fields
   * @param array $params Test parameters
   * @param string $message Error message to use if the check fails (will overwrite the default message)
   * @return void 
   **/
  function addCheck($check, $fields, $params = 0, $message = false) {
    if (($check == '') or !is_array($fields) or !count($fields)) return false;
    $key = md5($check . serialize($fields) . serialize($params) . serialize($message)); 
    $this->_sess_space['checks'][$key] = array($check, $fields, $params, $message);    
  }
  
  /**
   * Executes the validation methods stored using addCheck()
   **/
  function performCheck() {
    foreach ($this->_sess_space['checks'] as $check) {
      call_user_func_array(array(&$this, "check"), $check);
    }
  } 
  
  /**
   * Assigns form values using an associative array (key name => field name)
   * 
   * @param array $array Array of field name => field value 
   * @param bool $force overwrite even if a value is already set
   **/
  function assignArray(&$array, $force = false) {
    if (!is_array($array)) return false;
    foreach ($array as $key => $value) {
      if (is_string($key)) {
        $this->assign($key, $value, $force);
      }
    }
  }

}
?>
