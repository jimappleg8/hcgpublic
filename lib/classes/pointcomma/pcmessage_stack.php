<?php
/**
 * Project PointComma - Message Pile Class - message_stack.php
 * 
 * Warning FIFO STACK !!!
 * 
 * Call to handle every message stack in PointComma
 * More particularly Error message to user and Debugging Stack
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 23 févr. 2005
 * @version 0.1
 * 
 */

 class messageStack {
   /**
    * @var array The messageStack data.
    * @access private
    */
    var $_stack = array();
    var $_type = '';
    
   /**
    * Constructor of the class
    * 
    * Register the stack in the session if it is not the case
    * Get the stack for local modification
    * 
    * @var array The messageStack data.
    * @access private
    */
    
    function messageStack($strStackType) {
      if (($strStackType!='debug')&&($strStackType!='error')) {
        // error
        trigger_error('Bad message Stack Type');
      }
      
      //get the type
      $this->_type = $strStackType;
              
      if (isset($_SESSION['array'.$strStackType.'MessageStack'])) {
        //we get the data from the session 
        //for ($i=0; $i<sizeof($_SESSION['array'.$strStackType.'MessageStack']); $i++) {
        //  $this->push($_SESSION['array'.$strStackType.'MessageStack'][$i]);
        //}
        $this->_stack = $_SESSION['array'.$strStackType.'MessageStack']; 
      }
      else {
        //we register the array in the session
        $_SESSION['array'.$strStackType.'MessageStack'] = array();
      }   
    }   
    
    /**
    * Save the current stack into session.
    * @param mixed $content The element to be pushed onto the stack.
    * @access public
    */  
    function _saveStack() {
      //get the type of the stack
      $strStackType = $this->_type;
      //we save the data in the session
      $_SESSION['array'.$strStackType.'MessageStack']= &$this->_stack;
    }
    
    
   /**
    * Push the argument onto the stack.
    * @param mixed $content The element to be pushed onto the stack.
    * @access public
    */
    function push($element) {
        global $pcConfig;
        array_push($this->_stack, $element);
        //release old message
        
        if (isset($pcConfig[$this->_type]['stackMaxSize']) and ($this->size() > $pcConfig[$this->_type]['stackMaxSize'])) {
          //drop the first message in the stack
          $this->pop();
        }
        
        //save into session
        $this->_saveStack();
    }
    
   /**
    * Pop the Stack.
    * @access public
    * @return mixed The reference to the popped element.
    */
    function pop() {
        if ($this->size()==0) {
          return false;
        }
        $element = $this->_stack[0];
        array_shift($this->_stack);
         
        //save into session
        $this->_saveStack();
        
        return $element;
    }
    
   /**
    * Get the lenght of the Stack.
    * @access public
    * @return int The lenght of the Stack.
    */
    function size() {
        return count($this->_stack);
    }
    
    /**
    * Reset the Stack.
    * 
    * @access public
    * @return the last message in the Stack or false if the stack is empty.
    */
    function resetStack() {
      $msgLastMessage = false;
      if ($this->size()) {
        $msgLastMessage = $this->_stack[$this->size() -1];
      } 
     
      $this->_stack = array();
      $this->_saveStack();
      return $msgLastMessage;    
    }
}
  

?>
