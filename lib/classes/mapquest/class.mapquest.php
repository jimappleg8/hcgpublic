<?

class mapquest {

	/*
	 *
	 * updated 2002-03-17:
	 * _
	 * added some additional comments in the $mq_string array (below) ... explains how 
	 * to request maps for different countries
	 * _
	 * also note that the addQvar() method now allows you to add any variable/value pair 
	 * to the query string, even if it wasn't declared in the array.
	 *
	 *
	 * updated 2002-03-08:
	 * _
	 * added the zoom() method, you can add a value in the range 0-9 -- see example.php
	 * _
	 * added aerial view variable to the query string just for fun
	 * _
	 * fixed the addQvar() method to be compatible with PHP < 4.1.0
	 * _
	 * also note that you can't add "size=big" to the query string because mapquest seems to have blocked access to
	 * this via request string.  you can see the variable get passed in a request string from the site but they 
	 * must have something to check against internally ... probably because there are more ads on the small map
	 * _
	 *
	 *
	 *
	 * if you decide to add something cool (like a regex to monkey proof the address part)
	 * then please send me a copy.
	 * _
	 * see example.php
	 * _
	 * kumar@chicagomodular.com
	 *
	 * 
	 *
	 *
	 */

	
	/*
	 * public vars :
	 *
	 */
	var $mq_url = "http://mapquest.com/maps/map.adp"; //if this ever changes, you will need to edit
	var $mq_qstring=array( //use the addQvar method or set defaults here
	
						"country="=>"US",
						/* 
						 * you will have to study the results from the country select box here:
						 * http://mapquest.com/maps/main.adp to find the abbreviation for the country
						 * you are requesting a map from.  
						 * for example, Germany is "DE"
						 *
						 */
						"address="=>"",
						"city="=>"chicago",
						"state="=>"IL",
						"zipcode="=>"",
						"zoom="=>"8", //default for zoom -- the range is 0 - 9
						"dtype="=>"s", //s = streetmap, a= aerial view
										
						);

	var $a_css = ""; //no css class by default, otherwise make the var = "name_of_class"
	var $a_target = "_blank"; //if you don't like new windows, make it "_top"
	var $a_text = "check mapquest.com"; //what to show as the link text
	var $a_extra = ""; 	//use this for javascript tags .. 
						//there will be no formatting so 
						//make the var = "onmouseover=\"javascript:doSomething();\"" , etc.
	
	/*
	 * private vars :
	 *
	 */

	var $error = "";
	var $mq = ""; //each method puts the result here
	
	//--------------------------------------------------------------||
	
	
	/*
	 * fill the query string array :
	 *
	 */
	function addQvar($Qvar,$Qvar_val){
		$Qvar = (!eregi("=",$Qvar)) ? $Qvar . "=": $Qvar;
		$this->mq_qstring[$Qvar] = $Qvar_val;
		return TRUE;
	}
	 
	/*
	 * makes $mq a full <a> tag :
	 *
	 */
	function makeA(){
	
		//init
		$this->mq .= "\n<a ";
		if(!empty($this->a_css)) $this->mq .= "class=".$this->a_css." "; 
		$this->mq .= "href=\"";
		$this->makeHREF();
		$this->mq .= "\" target=".$this->a_target." ";
		if(!empty($this->a_extra)) $this->mq .= $this->a_extra." "; 
		$this->mq .= ">".$this->a_text."</a>\n";
		return TRUE;
	}
	
	/*
	 * makes $mq just the URL :
	 *
	 */
	function makeHREF(){
	
		$this->mq .= $this->mq_url; 
		
		//make query string:
		if(!is_array($this->mq_qstring)){
			$this->error="mq_qstring was not an array"; //just in case you were monkeying with $mq_qstring
			return FALSE;
		} else {
			$c=1;
			foreach($this->mq_qstring as $key => $val){
				
				if($c>1){
					$this->mq .= "&";
				} else {
					$this->mq .= "?";
				}
				$this->mq .= $key;
				$this->mq .= urlencode($val);
				$c++; //no offense to PHP
			}
			return TRUE;
		}
	}
	
	/*
	 * self-explanatory :
	 *
	 */
	 
	function zoom($val){
		//0 - 9 is the range
		if($val > 9 || $val < 0) $val=8; //if out of range, back to default
		$this->mq_qstring['zoom='] = $val;
		return TRUE;
	}
	
	function printA(){
		$this->makeA();
		print $this->mq;
		return TRUE;
	}
	
	function printHREF(){
		$this->makeHREF();
		print $this->mq;
		return TRUE;
	}

}

?>