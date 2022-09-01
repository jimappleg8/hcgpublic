<?php
/**
 * Filter class used by the formsess prefilter
 *
 * @author Katana <katana@katana-inc.com>
 * @package formsess
 * @version $Id: fs_filter.class.php,v 1.4 2003/08/12 12:39:32 katana Exp $
 **/
class fs_filter {
  
  /**
   * Properties used in the prefilter
   **/
  var $_source;                  // source code
  var $_tag_properties;          // array containing the tag properties
  var $_tag;                     // current tag
  var $_tag_name;                // tag name
  var $_is_closing_tag;          // tells wether the tag is a closing tag
  var $_has_inline_closing;      // tells wether the current tag is closed inline (<fs: ... />)  
  var $_tags_stack;              // stack for not inline closed tags
  var $_current_select;          // "block tag" (option, select) currently opened
  var $_current_form;            // current form name. False if no form is opened
  var $_mandatory_params;        // list of mandatory params for tags
  var $_validators;              // validators data stack for the current form        
  var $_tags = array();          // array listing the tags found in the current form
  var $_current_line = false;    // current line number
  var $_form_multipart_formdata; // indicates if the current form is set as enctype=multipart/form-data
  var $_fields_types = array();  // holds a list of the field types for the current form
  
  /**
   * Internal path to formsess' components
   **/
  var $_filterfuncs_path;   // path to filter functions files    
  var $_validators_path;    // path to validator functions files

  var $_smarty;               // Smarty object

  /**
   * Constructor for the filter.
   * Performs the filtering and stored the filtered source
   * 
   * @param string $source Source being filtered
   * @param Smarty $smarty Smarty object filtering the content
   **/
  function fs_filter($source, &$smarty) {
    $this->_smarty            = $smarty;
    $this->_source            = $source;
    $this->_current_select    = false;
    $this->_current_form      = false;
    $this->_tags_stack        = array();
    $this->_validators        = array();        
    $this->_filterfuncs_path  = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'filterfuncs' . DIRECTORY_SEPARATOR;
    $this->_validators_path   = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'validators'  . DIRECTORY_SEPARATOR;
      
    // list of mandatory parameters for each tag
    $this->_mandatory_params = array(
      'form'           => array('name', 'action', 'method'),
      'input'          => array('name', 'type'),
      'textarea'       => array('name'),
      'select'         => array('name'),
      'option'         => array('value'),
      'errormessage'   => array('field'),
      'iferrormessage' => array('field'),
      'validate'       => array('field', 'check'),
    );
    
    $this->_filter();        
  }
  
  /**
   * Returns the filtered source
   * 
   * @return string filtered source 
   **/
  function getSource() {
    return $this->_source;
  }
  
  /**
   * Starts the filtering
   * 
   * @return 
   **/
  function _filter() {
    // first, look for all <fs:(*)> tags
    // @todo add a condition to check if we don't at the same time get a closing and inline closing tag
    if (!preg_match_all('!<(/?)fs:([\w]+)(.*?)?(/?)>!xsmi', $this->_source, $fs_tags, PREG_SET_ORDER)) {
      return;
    }

    // store the option tags position from that match (temp trick until i find out more about conditionnal subpatterns)
    $free_for_options = array();
    for ($i = 0, $count = count($fs_tags); $i < $count; $i++) {
      if ($fs_tags[$i][2] == 'option') {
        // store the opening option tags positions
        if ($fs_tags[$i][1] != '/') {
          unset($fs_tags[$i]);
          $free_for_options[] = $i;
        }
        // delete the closing tag
        else {
          unset($fs_tags[$i]);
        }        
      }
    }
    
    // capture the option tags with their output
    preg_match_all('!<fs:option (.*?)>(.*?)</fs:option>!smi', $this->_source, $m, PREG_SET_ORDER);

    // then foreach found option add the output to the matched option entry back with the output into $fs_tags at the correct index
    foreach ($m as $match) {
      $pos = array_shift($free_for_options);
      $fs_tags[$pos] = array(
        0 => $match[0],
        1 => '',
        2 => 'option',
        3 => sprintf('%s output="%s"', $match[1], $match[2]),
        4 => '/'
      );
    }
    unset($free_for_options, $m, $match);
    ksort($fs_tags);
    
    // handle the tags
    foreach ($fs_tags as $fs_item) {
      $this->_tag                = $fs_item[0]; unset($fs_item[0]);
      $this->_tag_name           = strtolower($fs_item[2]); unset($fs_item[2]);
      $this->_is_closing_tag     = ($fs_item[1] == '/') ? true : false; unset($fs_item[1]);
      $this->_has_inline_closing = ($fs_item[4] == '/') ? true : false; unset($fs_item[4]);
      $this->_set_tag_properties($fs_item[3]); unset($fs_item[3]);
      if (!$this->_check_mandatory_params()) return false; // parameters
      if (!$this->_has_valid_context('pre')) return false; // semantic context

      // if the tag is not inline closed nor a closing tag, add it to the stack
      if (!$this->_is_closing_tag && !$this->_has_inline_closing) {
        if (!isset($this->_tags_stack[$this->_tag_name])) {
          $this->_tags_stack[$this->_tag_name] = 1;
        } else {
          $this->_tags_stack[$this->_tag_name]++;
        }
      }
      
      // replace the original tag in the source with the parsed value
      $parsed_tag = $this->_parse_tag();
      if ($parsed_tag !== false) {
        $this->_source = preg_replace('/' . preg_quote($this->_tag, '/') . '/', $parsed_tag, $this->_source, 1);
      }                          
      
      // if it's a closing tag, attempt to close it in the stack
      if ($this->_is_closing_tag) {
        if (!isset($this->_tags_stack[$this->_tag_name]) or !$this->_tags_stack[$this->_tag_name]) {
          $this->_trigger_error("no opening tag matched this closing tag");
          continue;
        } else {
          $this->_tags_stack[$this->_tag_name]--;
        }
      }

      if (!$this->_has_valid_context('post')) return false; // semantic context
    }
    
    // check the tags stack to make sure no opening tag remains unbalanced
    foreach ($this->_tags_stack as $tag => $stack) {
      if ($stack > 0) {
        $this->_trigger_error("unbalanced tag", $tag);
        return false;
      }
    }
    
  }
  
  /**
   * Checks wether the context allows the current tag
   * (e.g. all tags but form are only allowed when <form> has been found first, option is only valid between <select>...)
   * 
   * @param string $step Parsing step. The opening tags are handled in the pre step, and the closing ones during the post step 
   * 
   * @return bool true if the context is valid, or false 
   **/
  function _has_valid_context($step) {
    // form tag
    if ($this->_tag_name == 'form') {
      // check if we're not attempting to open a form within another form
			if (!$this->_is_closing_tag && ($this->_current_form !== false) && ($step == 'pre')) {
        $this->_trigger_error("a form cannot be opened inside another form");
        return false;
      }
			// if closing form, check for an opened form
			elseif ($this->_is_closing_tag && ($this->_current_form === false) && ($step == 'post')) {
        $this->_trigger_error("unable to close a form, no form has been opened");
        return false;
      }
			// open the form
			elseif (!$this->_is_closing_tag && !$this->_has_inline_closing && ($step == 'pre')) {
				$this->_current_form = $this->_tag_properties['name'];
			}
			// close the form
			elseif ($this->_is_closing_tag && ($this->_current_form !== false) && ($step == 'post')) {
				$this->_current_form = false;
			}
    }
    
    // any other tag, checks if a form is opened
    else {
      // all the tags need to be inside a form
      if ($this->_current_form === false) {
        $this->_trigger_error("needs to be inside a form");
      }
      // an option has to be inside a select
      elseif ($this->_tag_name == 'option' or $this->_tag_name == 'options') { 
     		if ($this->_current_select === false) {
          $this->_trigger_error("needs to be inside a select");
          return false;
        }
      }
      // context validations for select: nested selects are forbidden, and a closing select must follow an opening one
      elseif ($this->_tag_name == 'select') {
        if (($this->_current_select !== false) && !$this->_is_closing_tag && ($step == 'pre')) {
          $this->_trigger_error("nested select are not permitted");
          return false;
        }
        elseif (($step == 'pre') && !$this->_is_closing_tag && ($step == 'pre')) {
          $this->_current_select = $this->_tag_properties['name'];
        }
        elseif (($step == 'post') && $this->_is_closing_tag && ($step == 'post')) {
          $this->_current_select = false;
        } 
      }
      // context validation for an input type field: the form has to have enctype="multipart/form-data"
      elseif (($this->_tag_name == 'input') && isset($this->_tag_properties['type']) && ($this->_tag_properties['type'] == 'file') && ($step == 'pre')) {
        if (!$this->_form_multipart_formdata) {
          $this->_trigger_error('You have to add enctype="multipart/form-data" to upload a file using a form');
        }
      }
    }
    return true;   
  }
  
  /**
   * fs_filter::_trigger_error()
   * 
   * @param $message
   **/
  function _trigger_error($message, $tag = false, $error = E_USER_WARNING) {
    $line = ($this->_current_line === false) ? $this->_get_line_number() : $this->_current_line;  
    $tag  = $tag ? $tag : $this->_tag_name;
        
    $message = "Formess Error [{$this->_smarty->_current_file}:$line ($tag)]: $message";
    trigger_error($message, E_USER_WARNING);
  }
  
  /**
   * Analyzes the string containing the parameters and extracts it to an array
   * 
   * @param string $paramsString string with the parameters, e.g. p1="foo" p2="bar" ...
   * @return void
   * @access private 
   **/
  function _set_tag_properties($paramsString) {
    $this->_tag_properties = array();
    
    // parse all the properties to an array
    // @todo add the test for the escaped double quotes
    // @todo add variables parsing: {$sthg should be replaced with $sthg
    if (!preg_match_all('/(\w+)="([^"]*)"/', $paramsString, $matches, PREG_SET_ORDER)) return;
    foreach ($matches as $props) {
      $this->_tag_properties[strtolower($props[1])] = $props[2];
    }
    
    // assign the tag type (e.g. not only input but input_radio and so on)
    switch ($this->_tag_name) {
    	case 'input': 
    		$this->_fields_types[$this->_tag_properties['name']] = $this->_tag_name . '_' . $this->_tag_properties['type'];
    		break;
    	default:
    		$this->_fields_types[$this->_tag_properties['name']] = $this->_tag_name;
    } // switch ($this->_tag_name)
  }
  
  /**
   * Checks for any tag if the mandatory parameters are found
   * 
   * @return bool true if all the parameters were found, or false 
   **/
  function _check_mandatory_params() {
    $tagname = ($this->_is_closing_tag ? '/' : '') . $this->_tag_name;
    if (isset($this->_mandatory_params[$tagname])) {
      foreach ($this->_mandatory_params[$tagname] as $param) {
        if (!isset($this->_tag_properties[$param])) {
          $this->_trigger_error("parameter $param is mandatory");
          return false;
        }
      }
    }
    return true;
  }
  
  /**
   * Parses the current tag
   * 
   * @return void 
   **/
  function _parse_tag() {
    $filter_file     = $this->_filterfuncs_path . strtolower($this->_tag_name) . '.php'; 
    $filter_function = '_fs_filter_' . strtolower($this->_tag_name);
    
    // check for the existence of the filter function file
    if (!file_exists($filter_file)) {
      $this->_trigger_error("filter function file $filter_file not found");
      return false; 
    }
    
    // check if the function is defined in the included file
    require_once $filter_file;
    if (!function_exists($filter_function)) {
      $this->_trigger_error("filter function $filter_function not found in $function_file");
      return false; 
    }

    // call the function
    return $filter_function($this);
  }
  
  /**
   * Parses the validation tags stored during a form processing to the correct validation tags
   *  - Client side validation: the matching file is included from validators/
   * and the function is called in order to add the corresponding javascript code block
   * - Server side validation: if the validation file exists, a call to the smarty function
   * fs_check is added. When the template is processed the check is stored in the session
   * in order to be called later on using formsess::performCheck()
   **/
  function _parse_validation() {
    
    // no validator, we will just replace with empty strings
    if (!count($this->_validators)) {
      $jsCheckString = $fsCheckString = $jsOnSubmitString = ''; 
    }

    // load and call the validator functions
    else {
      $jsCheckString = "\n<script language=\"Javascript\">\n<!--\n{literal}function fsvalidate_{$this->_current_form}(tf) {\n";
      $fsCheckString = '';
      $jsOnSubmitString = sprintf('return fsvalidate_%s(this);', $this->_current_form);
      foreach ($this->_validators as $validator) {
        
        $this->_tag_name     = $validator['tag'];  unset($validator['tag']);
        $this->_current_line = $validator['line']; unset($validator['line']);
        
        // boolean values used to check if at least one version of the validator is found
        $jsFound = $fsFound = false;
        
        // preprocess the parameters
        $fields = array(0 => array('name' => $validator['field'])); unset($validator['field']);
        if (isset($validator['message'])) {
          $message = $validator['message'];
          unset($validator['message']);
        } else {
          $message = false;
        }
        
        $check = $validator['check']; unset($validator['check']);
  
        $params = array();
        foreach($validator as $name => $value) {
          // field* params are special and are used to provide the validator additionnal field to use
          if (preg_match('/^field[0-9]*$/', $name)) {
            $fields[] = array('name' => $value);
            unset($validator[$name]);
          }
          else {
            $params[$name] = $value;
          }
        }
        
        // client side validation
        $validator_file = $this->_validators_path . 'js.' . strtolower($check) . '.php'; 
        $validator_name = 'fs_jsvalidation_callback_' . strtolower($check);
        
        // check for the existence of the filter function file
        if (file_exists($validator_file)) {

          require_once $validator_file;
          // check if the function is defined in the included file
          if (!function_exists($validator_name)) {
            $this->_trigger_error("validation function $validator_name not found in $validator_file");
          }
          // call the function
          else {
            if ($ret = $validator_name($fields, $params, $this, $message)) {
              $jsCheckString .= $ret;
            }
            $jsFound = true;
          }
        }
        
        // Server side validation
        $validator_file = $this->_validators_path . strtolower($check) . '.php'; 
        $validator_name = 'fs_validation_callback_' . strtolower($check);
        if (file_exists($validator_file)) {
          
          require_once $validator_file;
          
          // check if the function is defined in the included file
          if (!function_exists($validator_name)) {
            $this->_trigger_error("validation function $validator_name not found in $validator_file");
          }
          
          // add a call to the smarty function fs_check
          else {
            $fsCheckString .= sprintf('{fs_check check="%s"', $check);
            foreach($fields as $id => $field) {
              // the [] string is removed for server side validation
              $fsCheckString .= sprintf(' field%s="%s"', ($id == 0 ? '' : $id), str_replace('[]', '', $field['name'])); 
            }
            foreach($params as $p_name => $p_value) {
              $fsCheckString .= sprintf(' %s="%s"', $p_name, str_replace('}', '##FS_CBRCKT##', $p_value));
            }
            if ($message) $fsCheckString .= sprintf(' message="%s"', $message);
            $fsCheckString .= '}';
            
            $fsFound = true;
          }
        }      
        if (!$fsFound && !$jsFound) $this->_trigger_error("No javascript nor formsess validator found for \"$validator_name\"");
      }
      $jsCheckString   .= "return true;\n}{/literal}\n-->\n</script>\n";
    }
    $this->_validators = array();
    
    // js function call onSubmit
    $this->_source = str_replace(sprintf('<!--_fs_check_%s /-->',       $this->_current_form), $fsCheckString,    $this->_source);
    $this->_source = str_replace(sprintf('<!--_fs_js_form_%s /-->',     $this->_current_form), $jsCheckString,    $this->_source);
    $this->_source = str_replace(sprintf('<!--_fs_js_onSubmit_%s /-->', $this->_current_form), $jsOnSubmitString, $this->_source);
  }
  
  /**
   * Stores a tag's type
   * 
   * @param string $name field's name
   * @param string $type field's type
   **/
  function _store_tag($name, $type) {
    $this->_tags[$name] = $type; 
  }
  
  /**
   * Returns a field type previously stored using _store_tag()
   * 
   * @param string $fname field's name
   * @return string the field type, or false if it was not stored
   **/
  function get_field_type($fname) {
    return isset($this->_fields_types[$fname]) ? $this->_fields_types[$fname] : false;
  }
  
  /**
   * Returns the line number for the current tag
   * 
   * @return int 
   **/
  function _get_line_number() {
    return substr_count(substr($this->_source, 0, strpos($this->_source, $this->_tag)), "\n");  
  }
}

?>