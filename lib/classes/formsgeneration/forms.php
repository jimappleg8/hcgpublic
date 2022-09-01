<?php
if(!defined("PHP_LIBRARY_FORMS"))
{
	define("PHP_LIBRARY_FORMS",1);

/*
 * forms.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/forms/forms.php,v 1.165 2004/07/14 08:39:47 mlemos Exp $
 *
 */

Function FormAppendOutput($output)
{
	global $form_output;

	$form_output.=$output;
}

Function FormDisplayOutput($output)
{
	echo $output;
}

class form_custom_class
{
	var $input;
	var $valid_marks=array();
	var $format="";
	var $focus_input="";
	var $mark_start="{";
	var $mark_end="}";

	var $format_data;
	var $format_marks;
	var $children=array();

	Function GenerateInputID(&$form, $input, $kind)
	{
		return("_".$input."_".$kind);
	}

	Function ParseFormat($format, $valid_marks, &$data, &$marks)
	{
		$mark_start_length=strlen($this->mark_start);
		$mark_end_length=strlen($this->mark_end);
		for($data=$marks=array(), $length=strlen($format), $position=0; $position<$length;)
		{
			if(GetType($next=strpos($format,$this->mark_start,$position))=="integer")
			{
				$data[]=substr($format,$position,$next-$position);
				if(GetType($following=strpos($format,$this->mark_end,$next+$mark_start_length))=="integer")
				{
					$mark=substr($format,$next+$mark_start_length,$following-$next-$mark_start_length);
					if(!IsSet($valid_marks["input"][$mark]))
						return("it was specified a invalid format mark (".$mark.") at position ".$next);
					$marks[]=array("input"=>$valid_marks["input"][$mark]);
					$position=$following+$mark_end_length;
				}
				else
					return("it was specified an unfinished format mark at position ".$next);
			}
			else
			{
				$data[]=substr($format,$position);
				break;
			}
		}
		return("");
	}

	Function ParseInputFormat()
	{
		if(!IsSet($this->format_data)
		&& strlen($error=$this->ParseFormat($this->format, $this->valid_marks, $this->format_data, $this->format_marks)))
		{
			UnSet($this->format_data);
			return($error);
		}
		return("");
	}

	Function ParseNewInputFormat()
	{
		UnSet($this->format_data);
		UnSet($this->format_marks);
		if(strlen($error=$this->ParseInputFormat()))
		{
			UnSet($this->format_data);
			UnSet($this->format_marks);
		}
		return($error);
	}

	Function AddFormattedPart(&$form, $data, $marks, $hidden)
	{
		for($part=0;$part<count($data);$part++)
		{
			if((strlen($data[$part])
			&& strlen($error=$form->AddDataPart($data[$part])))
			|| ($part<count($marks)
			&& strlen($error=($hidden ? $form->AddInputHiddenPart($marks[$part]["input"]) : $form->AddInputPart($marks[$part]["input"])))))
				return($error);
		}
		return("");
	}

	Function AddInput(&$form, $arguments)
	{
		return("form input custom class does not implement the AddInput function");
	}

	Function AddInputPart(&$form)
	{
		if(count($this->valid_marks)==0
		|| strlen($this->format)==0)
			return("form input custom class does not implement the AddInputPart function");
		if(strlen($error=$this->ParseInputFormat()))
			return($error);
		return($this->AddFormattedPart($form, $this->format_data, $this->format_marks, 0));
	}

	Function AddInputHiddenPart(&$form)
	{
		if(count($this->valid_marks)==0
		|| strlen($this->format)==0)
			return("form input custom class does not implement the AddInputHiddenPart function");
		if(strlen($error=$this->ParseInputFormat()))
			return($error);
		return($this->AddFormattedPart($form, $this->format_data, $this->format_marks, 1));
	}

	Function AddLabelPart(&$form, $arguments)
	{
		if(strlen($this->focus_input)==0)
			return("form input custom class does not implement the AddLabelPart function");
		$arguments["FOR"]=$this->focus_input;
		return($form->AddLabelPart($arguments));
	}

	Function ValidateInput(&$form)
	{
		return("form input custom class does not implement the Validate function");
	}

	Function DefaultSetInputProperty(&$form, $property, $value)
	{
		switch($property)
		{
			case "Format":
				$this->format=$value;
				if(strlen($error=$this->ParseNewInputFormat()))
					return($error);
				if(count($this->format_marks)==0)
				{
					$this->focus_input="";
					return("the input format does not specify any inputs");
				}
				$this->focus_input=$this->format_marks[0]["input"];
				break;
			case "Accessible":
			case "SubForm":
				for($child=0;$child<count($this->children);$child++)
				{
					$error=$form->SetInputProperty($this->children[$child], $property, $value);
					if(strlen($error))
						return($error);
				}
				break;
			default:
				return($property." is not a changeable form ".$this->input." input property");
		}
		return("");
	}

	Function SetInputProperty(&$form, $property, $value)
	{
		return($this->DefaultSetInputProperty($form, $property, $value));
	}

	Function GetInputValue(&$form)
	{
		return("");
	}

	Function AddFunction(&$form, $arguments)
	{
		if(strlen($this->focus_input)==0)
			return("form input custom class does not implement the AddFunction function");
		$arguments["Element"]=$this->focus_input;
		return($form->AddFunction($arguments));
	}

	Function LoadInputValues(&$form, $submitted)
	{
	}

	Function GetJavascriptValidations(&$form, $form_object, &$validations)
	{
		$validations=array();
		return("");
	}

	Function GetJavascriptInputValue($form, $form_object)
	{
		return("");
	}

	Function AddChild(&$form, $name)
	{
		$this->children[]=$name;
		return("");
	}
};

class form_class
{
	var $parts=array();
	var $inputs=array();
	var $types=array();
	var $NAME="";
	var $METHOD="";
	var $ACTION="";
	var $TARGET="";
	var $ONSUBMIT="";
	var $ONSUBMITTING="";
	var $ENCTYPE="";
	var $ONRESET="";
	var $ExtraAttributes=array();
	var $encoding="iso-8859-1";
	var $ValidationFunctionName="ValidateForm";
	var $ValidateAsEmail="ValidateEmail";
	var $ValidateAsCreditCard="ValidateCreditCard";
	var $ReadOnly=0;
	var $OutputPasswordValues=0;
	var $Changes=array();
	var $OptionsSeparator="\n";
	var $sub_form_variable_name="sub_form";
	var $email_regular_expression="^([-!#\$%&'*+./0-9=?A-Z^_`a-z{|}~])+@([-!#\$%&'*+/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,4}\$";
	var $client_validate=0;
	var $server_validate=0;
	var $accessibility_tab_index="";
	var $debug="";
	var $end_of_line="\n";
	var $input_parts=array();
	var $input_elements=array();
	var $functions=array();
	var $toupper_function="strtoupper";
	var $tolower_function="strtolower";
	var $form_submitted_variable_name="form_submitted";
	var $form_submitted_test_variable_name="form_submitted_test";
	var $ResubmitConfirmMessage="";
	var $allow_used_access_keys=1;
	var $label_access_keys=array();
	var $reserved_access_keys=array();
	var $hidden_parts=0;
	var $accessible_parts=0;
	var $error="";
	var $capturing=0;
	var $checkbox_inputs;
	var $radio_inputs;
	var $current_parent="";

	Function EncodeJavascriptString($string)
	{
		if(strlen($string)==0)
			return("''");
		$encode=!strcmp(strtolower($this->encoding),"iso-8859-1");
		for($last_encoded=0,$js_string="",$character=0;$character<strlen($string);$character++)
		{
			$code=Ord($string[$character]);
			if($code<32
			|| ($code>127
			&& $encode))
			{
				if($character!=0)
				{
					if(!$last_encoded)
						$js_string.="'";
					$js_string.="+";
				}
				$js_string.="unescape('%".sprintf("%02X",$code)."')";
				$last_encoded=1;
			}
			else
			{
				if($last_encoded)
					$js_string.="+'";
				else
				{
					if($character==0)
						$js_string.="'";
				}
				if($string[$character]=="'")
					$js_string.="\\";
				$js_string.=$string[$character];
				$last_encoded=0;
			}
		}
		if(!$last_encoded)
			$js_string.="'";
		return($js_string);
	}

	Function EncodeHTMLString($string)
	{
		switch(strtolower($this->encoding))
		{
			case "iso-8859-1":
				return(HtmlSpecialChars($string));
			default:
				return(HtmlEntities($string));
		}
	}

	Function EscapeJavascriptRegularExpressions($expression)
	{
		return(str_replace("\t","\\t",str_replace("\n","\\n",str_replace("\r","\\r",str_replace("\"","\\\"",str_replace("\\","\\\\",$expression))))));
	}

	Function OutputError($error,$scope="")
	{
		$this->error=(strcmp($scope,"") ? $scope.": ".$error : $error);
		if(strcmp($function=$this->debug,"")
		&& strcmp($this->error,""))
			$function($this->error);
		return($this->error);
	}

	Function AddInput($arguments)
	{
		if(strcmp(GetType($arguments),"array"))
			return($this->OutputError("it was not specified a valid arguments array","AddInput"));
		$input=array();
		$name="";
		if(IsSet($arguments["NAME"])
		&& strcmp($arguments["NAME"],""))
			$name=$input["NAME"]=$arguments["NAME"];
		if(IsSet($arguments["ID"])
		&& strcmp($arguments["ID"],""))
			$name=$input["ID"]=$arguments["ID"];
		if(!strcmp($name,""))
			return($this->OutputError("it was not specified a valid input name","AddInput"));
		if(IsSet($this->inputs[$name]))
			return($this->OutputError("it was specified the name of an already defined input",$name));
		if(!IsSet($arguments["TYPE"]))
			return($this->OutputError("it was not defined the type of form input element",$name));
		$needs_client_error_message=$needs_server_error_message=0;
		if(IsSet($arguments["ValidateAsEmail"]))
		{
			if(IsSet($arguments["ValidateAsEmailErrorMessage"])
			&& strcmp($arguments["ValidateAsEmailErrorMessage"],""))
				$input["ValidateAsEmailErrorMessage"]=$arguments["ValidateAsEmailErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=$input["ValidateAsEmail"]=1;
		}
		if(IsSet($arguments["ValidateAsCreditCard"]))
		{
			switch($arguments["ValidateAsCreditCard"])
			{
				case "field":
					$field=(IsSet($arguments["ValidationCreditCardTypeField"]) ? $arguments["ValidationCreditCardTypeField"] : "");
					$field_type=(IsSet($this->inputs[$field]) ? $this->inputs[$field]["TYPE"] : "");
					switch($field_type)
					{
						case "text":
						case "select":
							break;
						case "radio":
						case "password":
						case "submit":
						case "image":
						case "reset":
						case "button":
						case "textarea":
						case "checkbox":
						case "hidden":
						default:
							return($this->OutputError("it was not specified valid validation credit card type field",$name));
					}
					$input["ValidationCreditCardTypeField"]=$arguments["ValidationCreditCardTypeField"];
				case "mastercard":
				case "visa":
				case "amex":
				case "dinersclub":
				case "carteblanche":
				case "discover":
				case "enroute":
				case "jcb":
				case "unknown":
					$input["ValidateAsCreditCard"]=$arguments["ValidateAsCreditCard"];
					break;
				default:
					return($this->OutputError("it was not specified valid credit card type",$name));
			}
			if(IsSet($arguments["ValidateAsCreditCardErrorMessage"])
			&& strcmp($arguments["ValidateAsCreditCardErrorMessage"],""))
				$input["ValidateAsCreditCardErrorMessage"]=$arguments["ValidateAsCreditCardErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidateRegularExpression"]))
		{
			if(IsSet($arguments["ValidateRegularExpressionErrorMessage"])
			&& strcmp($arguments["ValidateRegularExpressionErrorMessage"],""))
				$input["ValidateRegularExpressionErrorMessage"]=$arguments["ValidateRegularExpressionErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ValidateRegularExpression"]=$arguments["ValidateRegularExpression"];
			$input["ServerValidate"]=$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidateAsNotEmpty"]))
		{
			if(IsSet($arguments["ValidateAsNotEmptyErrorMessage"])
			&& strcmp($arguments["ValidateAsNotEmptyErrorMessage"],""))
				$input["ValidateAsNotEmptyErrorMessage"]=$arguments["ValidateAsNotEmptyErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=$input["ValidateAsNotEmpty"]=1;
		}
		if(IsSet($arguments["ValidateMinimumLength"]))
		{
			$minimum_length=intval($arguments["ValidateMinimumLength"]);
			if(strcmp(strval($minimum_length),$arguments["ValidateMinimumLength"])
			|| $minimum_length<=0)
				return($this->OutputError("it was not specified a valid minimum field length to validate",$name));
			$input["ValidateMinimumLength"]=$minimum_length;
			if(IsSet($arguments["ValidateMinimumLengthErrorMessage"])
			&& strcmp($arguments["ValidateMinimumLengthErrorMessage"],""))
				$input["ValidateMinimumLengthErrorMessage"]=$arguments["ValidateMinimumLengthErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidateAsEqualTo"]))
		{
			if(!strcmp($arguments["ValidateAsEqualTo"],""))
				return($this->OutputError("it was not specified a valid comparision field",$name));
			$input["ValidateAsEqualTo"]=$arguments["ValidateAsEqualTo"];
			if(IsSet($arguments["ValidateAsEqualToErrorMessage"])
			&& strcmp($arguments["ValidateAsEqualToErrorMessage"],""))
				$input["ValidateAsEqualToErrorMessage"]=$arguments["ValidateAsEqualToErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidateAsDifferentFrom"]))
		{
			if(!strcmp($arguments["ValidateAsDifferentFrom"],""))
				return($this->OutputError("it was not specified a valid comnparision field",$name));
			$input["ValidateAsDifferentFrom"]=$arguments["ValidateAsDifferentFrom"];
			if(IsSet($arguments["ValidateAsDifferentFromErrorMessage"])
			&& strcmp($arguments["ValidateAsDifferentFromErrorMessage"],""))
				$input["ValidateAsDifferentFromErrorMessage"]=$arguments["ValidateAsDifferentFromErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidateAsSet"]))
		{
			switch($arguments["TYPE"])
			{
				case "radio":
				case "checkbox":
					break;
				case "select":
					if(IsSet($arguments["MULTIPLE"]))
						break;
				default:
					return($this->OutputError("it was not specified a valid field for as set validation",$name));
			}
			$input["ValidateAsSet"]=$arguments["ValidateAsSet"];
			if(IsSet($arguments["ValidateAsSetErrorMessage"])
			&& strcmp($arguments["ValidateAsSetErrorMessage"],""))
				$input["ValidateAsSetErrorMessage"]=$arguments["ValidateAsSetErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidateAsInteger"]))
		{
			if(IsSet($arguments["ValidationLowerLimit"]))
			{
				if(strcmp(GetType($limit=$arguments["ValidationLowerLimit"]),"integer"))
					return($this->OutputError("it was not specified a valid lower limit value",$name));
				$input["ValidationLowerLimit"]=$limit;
			}
			if(IsSet($arguments["ValidationUpperLimit"]))
			{
				if(strcmp(GetType($limit=$arguments["ValidationUpperLimit"]),"integer")
				|| $limit<$input["ValidationLowerLimit"])
					return($this->OutputError("it was not specified a valid upper limit value",$name));
				$input["ValidationUpperLimit"]=$limit;
			}
			if(IsSet($arguments["ValidateAsIntegerErrorMessage"])
			&& strcmp($arguments["ValidateAsIntegerErrorMessage"],""))
				$input["ValidateAsIntegerErrorMessage"]=$arguments["ValidateAsIntegerErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=$input["ValidateAsInteger"]=1;
		}
		if(IsSet($arguments["ValidateAsFloat"]))
		{
			if(IsSet($arguments["ValidationLowerLimit"]))
			{
				$limit_type=GetType($limit=$arguments["ValidationLowerLimit"]);
				if(strcmp($limit_type,"double")
				&& strcmp($limit_type,"integer"))
					return($this->OutputError("it was not specified a valid lower limit value",$name));
				$input["ValidationLowerLimit"]=$limit;
			}
			if(IsSet($arguments["ValidationUpperLimit"]))
			{
				$limit_type=GetType($limit=$arguments["ValidationUpperLimit"]);
				if((strcmp($limit_type,"double")
				&& strcmp($limit_type,"integer"))
				|| $limit<$input["ValidationLowerLimit"])
					return($this->OutputError("it was not specified a valid upper limit value",$name));
				$input["ValidationUpperLimit"]=$limit;
			}
			if(IsSet($arguments["ValidationDecimalPlaces"]))
			{
				if((strcmp(GetType($places=$arguments["ValidationDecimalPlaces"]),"integer")))
					return($this->OutputError("it was not specified a valid number of decimal places",$name));
				$input["ValidationDecimalPlaces"]=$places;
			}
			if(IsSet($arguments["ValidateAsFloatErrorMessage"])
			&& strcmp($arguments["ValidateAsFloatErrorMessage"],""))
				$input["ValidateAsFloatErrorMessage"]=$arguments["ValidateAsFloatErrorMessage"];
			else
			{
				$needs_client_error_message++;
				$needs_server_error_message++;
			}
			$input["ServerValidate"]=$input["ClientValidate"]=$input["ValidateAsFloat"]=1;
		}
		if(IsSet($arguments["ValidationClientFunction"]))
		{
			if(!strcmp($arguments["ValidationClientFunction"],""))
				return($this->OutputError("it was not specified a valid client validatation function",$name));
			$input["ValidationClientFunction"]=$arguments["ValidationClientFunction"];
			if(IsSet($arguments["ValidationClientFunctionErrorMessage"])
			&& strcmp($arguments["ValidationClientFunctionErrorMessage"],""))
				$input["ValidationClientFunctionErrorMessage"]=$arguments["ValidationClientFunctionErrorMessage"];
			else
				$needs_client_error_message++;
			$input["ClientValidate"]=1;
		}
		if(IsSet($arguments["ValidationServerFunction"]))
		{
			if(!strcmp($arguments["ValidationServerFunction"],""))
				return($this->OutputError("it was not specified a valid server validation function",$name));
			$input["ValidationServerFunction"]=$arguments["ValidationServerFunction"];
			if(IsSet($arguments["ValidationServerFunctionErrorMessage"])
			&& strcmp($arguments["ValidationServerFunctionErrorMessage"],""))
				$input["ValidationServerFunctionErrorMessage"]=$arguments["ValidationServerFunctionErrorMessage"];
			else
				$needs_server_error_message++;
			$input["ServerValidate"]=1;
		}
		if(IsSet($arguments["DiscardInvalidValues"])
		&& $arguments["DiscardInvalidValues"])
			$input["DiscardInvalidValues"]=1;
		if(IsSet($arguments["ValidationErrorMessage"])
		&& strcmp($arguments["ValidationErrorMessage"],""))
			$input["ValidationErrorMessage"]=$arguments["ValidationErrorMessage"];
		else
		{
			if(!IsSet($input["DiscardInvalidValues"])
			&& ($needs_client_error_message>0
			|| $needs_server_error_message>0))
				return($this->OutputError("it was not specified a valid validate error message",$name));
		}
		if(IsSet($arguments["ValidateOnlyOnClientSide"])
		&& $arguments["ValidateOnlyOnClientSide"]
		&& IsSet($arguments["ValidateOnlyOnServerSide"])
		&& $arguments["ValidateOnlyOnServerSide"])
				return($this->OutputError("it was specified to validate the input field value either only on client side and only on server side",$name));
		if(!IsSet($input["ClientValidate"])
		|| (IsSet($arguments["ValidateOnlyOnServerSide"])
		&& $arguments["ValidateOnlyOnServerSide"]))
			$input["ClientValidate"]=0;
		if(IsSet($input["ServerValidate"])
		&& (!IsSet($arguments["ValidateOnlyOnClientSide"])
		|| !$arguments["ValidateOnlyOnClientSide"]))
			$this->server_validate=1;
		else
			$input["ServerValidate"]=0;
		switch(($input["TYPE"]=$arguments["TYPE"]))
		{
			case "file":
				if(IsSet($arguments["ACCEPT"]))
					$input["ACCEPT"]=$arguments["ACCEPT"];
			case "text":
				if(IsSet($arguments["MAXLENGTH"]))
					$input["MAXLENGTH"]=$arguments["MAXLENGTH"];
				if(IsSet($arguments["SIZE"]))
					$input["SIZE"]=$arguments["SIZE"];
				break;
			case "password":
				if(IsSet($arguments["MAXLENGTH"]))
					$input["MAXLENGTH"]=$arguments["MAXLENGTH"];
				if(IsSet($arguments["SIZE"]))
					$input["SIZE"]=$arguments["SIZE"];
				if(IsSet($arguments["Encoding"]))
				{
					if(!strcmp($input["Encoding"]=$arguments["Encoding"],""))
						return($this->OutputError("it was not defined a valid password encoding function",$name));
					if(IsSet($arguments["EncodedField"]))
					{
						if(!strcmp($input["EncodedField"]=$arguments["EncodedField"],""))
							return($this->OutputError("it was not defined a valid password encoded field",$name));
					}
					if(IsSet($arguments["EncodingFunctionVerification"]))
					{
						if(!strcmp($input["EncodingFunctionVerification"]=$arguments["EncodingFunctionVerification"],""))
							return($this->OutputError("it was not defined a valid password encoding function verification",$name));
					}
					else
						$input["EncodingFunctionVerification"]=$arguments["Encoding"];
					if(IsSet($arguments["EncodingFunctionScriptFile"]))
					{
						if(!strcmp($input["EncodingFunctionScriptFile"]=$arguments["EncodingFunctionScriptFile"],""))
							return($this->OutputError("it was not defined a valid password encoding function script file",$name));
					}
				}
				break;
			case "checkbox":
				if(IsSet($arguments["MULTIPLE"])
				&& $arguments["MULTIPLE"])
					$input["MULTIPLE"]=1;
			case "radio":
				if(IsSet($arguments["CHECKED"])
				&& $arguments["CHECKED"])
					$input["CHECKED"]=1;
				break;
			case "image":
				if(!IsSet($arguments["SRC"]))
					return($this->OutputError("it was not defined a valid image button source",$name));
				$input["SRC"]=$arguments["SRC"];
				if(IsSet($arguments["ALT"]))
					$input["ALT"]=$arguments["ALT"];
				break;
			case "submit":
			case "reset":
			case "hidden":
			case "button":
				break;
			case "textarea":
				if(IsSet($arguments["ROWS"]))
				{
					$value=$arguments["ROWS"];
					if(strcmp(GetType($value),"integer")
					|| $value<=0)
						return($this->OutputError("it was not defined a valid number of ROWS",$name));
					$input["ROWS"]=$value;
				}
				if(IsSet($arguments["COLS"]))
				{
					$value=$arguments["COLS"];
					if(strcmp(GetType($value),"integer")
					|| $value<=0)
						return($this->OutputError("it was not defined a valid number of COLS",$name));
					$input["COLS"]=$value;
				}
				break;
			case "select":
				if(!IsSet($arguments["OPTIONS"])
				|| strcmp(GetType($arguments["OPTIONS"]),"array")
				|| count($arguments["OPTIONS"])==0)
					return($this->OutputError("it was not defined a valid options array",$name));
				for($option=0,Reset($arguments["OPTIONS"]);$option<count($arguments["OPTIONS"]);Next($arguments["OPTIONS"]),$option++)
					$options[Key($arguments["OPTIONS"])]=$arguments["OPTIONS"][Key($arguments["OPTIONS"])];
				if(IsSet($arguments["SIZE"]))
					$input["SIZE"]=$arguments["SIZE"];
				if(IsSet($arguments["MULTIPLE"])
				&& $arguments["MULTIPLE"])
				{
					$selected=array();
					if(IsSet($arguments["SELECTED"]))
					{
						if(strcmp(GetType($arguments["SELECTED"]),"array"))
							return($this->OutputError("it was not defined a valid options array",$name));
						for($option=0;$option<count($arguments["SELECTED"]);$option++)
						{
							$option_value=$arguments["SELECTED"][$option];
							if(!IsSet($options[$option_value]))
								return($this->OutputError("it specified a selected value that is not a valid option",$name));
							if(IsSet($selected[$option_value]))
								return($this->OutputError("it specified a repeated selected option value",$name));
							$selected[$option_value]=1;
						}
					}
					$input["SELECTED"]=$selected;
					$input["MULTIPLE"]=1;
				}
				else
				{
					if(!IsSet($arguments["VALUE"])
					|| (strcmp(GetType($arguments["VALUE"]),"string")
					&& strcmp(GetType($arguments["VALUE"]),"integer")
					&& strcmp(GetType($arguments["VALUE"]),"double"))
					|| !IsSet($options[$arguments["VALUE"]]))
						return($this->OutputError("it was not defined a valid input value",$name));
					$input["VALUE"]=strval($arguments["VALUE"]);
				}
				$input["OPTIONS"]=$options;
				break;
			case "custom":
				if(!IsSet($arguments["CustomClass"]))
					return($this->OutputError("it was not defined the custom input class",$name));
				break;
			default:
				return($this->OutputError("it was not defined a supported input element",$name));
		}
		if(IsSet($arguments["VALUE"]))
			$input["VALUE"]=$arguments["VALUE"];
		if(IsSet($arguments["ReadOnlyMark"]))
			$input["ReadOnlyMark"]=$arguments["ReadOnlyMark"];
		if(IsSet($arguments["TABINDEX"]))
			$input["TABINDEX"]=$arguments["TABINDEX"];
		if(IsSet($arguments["STYLE"]))
			$input["STYLE"]=$arguments["STYLE"];
		if(IsSet($arguments["CLASS"]))
			$input["CLASS"]=$arguments["CLASS"];
		if(IsSet($arguments["ONCHANGE"]))
			$input["ONCHANGE"]=$arguments["ONCHANGE"];
		if(IsSet($arguments["ONCLICK"]))
			$input["ONCLICK"]=$arguments["ONCLICK"];
		if(IsSet($arguments["ReplacePatterns"]))
		{
			if(GetType($arguments["ReplacePatterns"])!="array")
				return($this->OutputError("it was not specified valid ReplacePatterns array argument",$name));
			$input["ReplacePatterns"]=$arguments["ReplacePatterns"];
		}
		if(IsSet($arguments["Capitalization"]))
		{
			switch($arguments["Capitalization"])
			{
				case "uppercase":
				case "lowercase":
				case "words":
					$input["Capitalization"]=$arguments["Capitalization"];
					break;
				default:
					return($this->OutputError("it was not defined valid capitalization method attribute",$name));
			}
		}
		if(IsSet($arguments["LABEL"]))
		{
			if(strlen($arguments["LABEL"])==0)
				return($this->OutputError("it was not defined valid input LABEL",$name));
			$input["LABEL"]=$arguments["LABEL"];
		}
		if(IsSet($arguments["ACCESSKEY"]))
		{
			if(strlen($arguments["ACCESSKEY"])==0)
				return($this->OutputError("it was not defined valid input ACCESSKEY",$name));
			$input["ACCESSKEY"]=$arguments["ACCESSKEY"];
		}
		if(IsSet($arguments["ExtraAttributes"]))
			$input["ExtraAttributes"]=$arguments["ExtraAttributes"];
		if(IsSet($arguments["ClientScript"]))
			$input["ClientScript"]=$arguments["ClientScript"];
		if(IsSet($arguments["ValidateOptionalValue"]))
			$input["ValidateOptionalValue"]=$arguments["ValidateOptionalValue"];
		$current_parent=$this->current_parent;
		if(strlen($current_parent))
		{
			$input["parent"]=$current_parent;
			if(IsSet($this->inputs[$this->current_parent]["Accessible"])
			&& !IsSet($arguments["Accessible"]))
				$arguments["Accessible"]=$this->inputs[$this->current_parent]["Accessible"];
			if(strlen($this->inputs[$this->current_parent]["SubForm"])
			&& !IsSet($arguments["SubForm"]))
				$arguments["SubForm"]=$this->inputs[$this->current_parent]["SubForm"];
		}
		if(IsSet($arguments["Accessible"]))
			$input["Accessible"]=$arguments["Accessible"];
		$input["SubForm"]=(IsSet($arguments["SubForm"]) ? $arguments["SubForm"] : "");
		$this->inputs[$name]=$input;
		if(!strcmp($input["TYPE"],"custom"))
		{
			$this->inputs[$name]["object"]=new $arguments["CustomClass"];
			$this->inputs[$name]["object"]->input=$name;
			$this->current_parent=$name;
			$error=$this->inputs[$name]["object"]->AddInput($this, $arguments);
			$this->current_parent=$current_parent;
			if(strlen($error))
			{
				$this->OutputError($error,$name);
				UnSet($this->inputs[$name]);
				return($error);
			}
		}
		if(strlen($current_parent))
			$this->inputs[$current_parent]["object"]->AddChild($this, $name);
		return("");
	}

	Function AddDataPart($data)
	{
		if($this->capturing)
		{
			$data=ob_get_contents().$data;
			ob_end_clean();
			if(!$this->capturing=ob_start())
				return($this->OutputError("could not resume layout capturing","AddDataPart"));
		}
		if(strlen($data))
		{
			$this->parts[]=$data;
			$this->types[]="DATA";
		}
		return("");
	}

	Function SetInputElement($element)
	{
		if(IsSet($this->inputs[$element]["NAME"]))
		{
			$name=$this->inputs[$element]["NAME"];
			if(IsSet($this->input_elements[$name]))
			{
				$input_element="elements[".$this->EncodeJavascriptString($name)."]";
				if(count($this->input_elements[$name])==1)
					$this->inputs[$this->input_elements[$name][0]]["InputElement"]="elements[".$this->EncodeJavascriptString($this->inputs[$this->input_elements[$name][0]]["NAME"])."][0]";
				$input_element.="[".count($this->input_elements[$name])."]";
				$this->input_elements[$name][]=$element;
			}
			else
			{
			  $input_element=$name;
				$this->input_elements[$name]=array($element);
			}
			$this->inputs[$element]["InputElement"]=$input_element;
		}
		else
		{
			if($this->inputs[$element]["ClientValidate"])
				return($this->OutputError("it was specified an unnamed input to validate",$element));
			if(IsSet($this->inputs[$element]["Encoding"]))
				return($this->OutputError("it was specified an unnamed input to encode",$element));
			$this->inputs[$element]["InputElement"]="";
		}
		return("");
	}

	Function AddInputPart($input)
	{
		if($this->capturing)
			$this->AddDataPart("");
		if(!IsSet($this->inputs[$input]))
			return($this->OutputError("it was not specified a valid input (1)",$input));
		if(!strcmp($this->inputs[$input]["TYPE"],"custom")
		&& strlen($error=$this->inputs[$input]["object"]->AddInputPart($this)))
		{
			$this->OutputError($error,$input);
			return($error);
		}
		if(IsSet($this->inputs[$input]["Accessible"]))
		{
			if(($read_only=!$this->inputs[$input]["Accessible"]))
				$type="READ_ONLY_INPUT";
			else
			{
				$type="ACCESSIBLE_INPUT";
				$this->accessible_parts++;
			}
		}
		else
		{
			$read_only=$this->ReadOnly;
			$type="INPUT";
		}
		if(!$read_only)
		{
			if(strcmp($error=$this->SetInputElement($input),""))
				return($error);
			$this->input_parts[]=$input;
			if($this->inputs[$input]["ClientValidate"])
				$this->client_validate=1;
		}
		$this->inputs[$input]["Part"]=count($this->parts);
		$this->parts[]=$input;
		$this->types[]=$type;
		return("");
	}

	Function AddInputHiddenPart($input)
	{
		if($this->capturing)
			$this->AddDataPart("");
		if(!IsSet($this->inputs[$input]))
			return($this->OutputError("it was not specified a valid input (2)",$input));
		if(!strcmp($this->inputs[$input]["TYPE"],"custom")
		&& strlen($error=$this->inputs[$input]["object"]->AddInputHiddenPart($this)))
		{
			$this->OutputError($error,$input);
			return($error);
		}
		if(strcmp($error=$this->SetInputElement($input),""))
			return($error);
		$this->inputs[$input]["Part"]=count($this->parts);
		$this->input_parts[]=$input;
		$this->parts[]=$input;
		$this->types[]="HIDDEN_INPUT";
		$this->hidden_parts++;
		return("");
	}

	Function AddLabelPart($arguments)
	{
		if($this->capturing)
			$this->AddDataPart("");
		if(!IsSet($arguments["FOR"])
		|| !strcmp($for=$label["FOR"]=$arguments["FOR"],"")
		|| !IsSet($this->inputs[$for])
		|| !IsSet($this->inputs[$for]["ID"]))
			return($this->OutputError("it was not specified a valid label FOR input ID","AddLabelPart"));
		if(IsSet($arguments["LABEL"]))
			$label["LABEL"]=$arguments["LABEL"];
		else
		{
			if(IsSet($this->inputs[$for]["LABEL"]))
				$label["LABEL"]=$this->inputs[$for]["LABEL"];
			else
				$label["LABEL"]="";
		}
		if(strlen($label["LABEL"])==0)
			return($this->OutputError("it was not specified a valid label",$for));
		if(IsSet($arguments["ACCESSKEY"]))
			$label["ACCESSKEY"]=$arguments["ACCESSKEY"];
		else
		{
			if(IsSet($this->inputs[$for]["ACCESSKEY"]))
				$label["ACCESSKEY"]=$this->inputs[$for]["ACCESSKEY"];
			else
				$label["ACCESSKEY"]="";
		}
		if(!strcmp($this->inputs[$for]["TYPE"],"custom"))
		{
			if(strlen($error=$this->inputs[$for]["object"]->AddLabelPart($this, $label)))
			{
				$this->OutputError($error, $for);
				return($error);
			}
			return("");
		}
		if(strlen($label["ACCESSKEY"])==0)
			Unset($label["ACCESSKEY"]);
		else
		{
			$lower=$this->tolower_function;
			$key=$lower($label["ACCESSKEY"]);
			if(!$this->allow_used_access_keys)
			{
				if(IsSet($this->label_access_keys[$key]))
					return($this->OutputError("it was specified label FOR input \"".$label["FOR"]."\" that was already specified for input \"".$this->label_access_keys[$key]."\"",$for));
			}
			if(IsSet($this->reserved_access_keys[$key]))
				return($this->OutputError("it was specified label FOR input \"".$label["FOR"]."\" that is already reserved for \"".$this->reserved_access_keys[$key]."\"",$for));
			$this->label_access_keys[$key]=$for;
		}
		$this->parts[]=$label;
		$this->types[]="LABEL";
		return("");
	}

	Function AddFunction($arguments)
	{
		$name="AddFunction";
		if(!IsSet($arguments["Function"])
		|| !strcmp($name=$arguments["Function"],""))
			return($this->OutputError("it was not specified a valid function name",$name));
		if(IsSet($this->functions[$name]))
			return($this->OutputError("it was specified an already existing function",$name));
		$function=array();
		switch($function["Type"]=(IsSet($arguments["Type"]) ? $arguments["Type"] : ""))
		{
			case "focus":
			case "select":
			case "select_focus":
			case "disable":
			case "enable":
				if(IsSet($arguments["Element"])
				&& IsSet($this->inputs[$input=$arguments["Element"]]))
				{
					if(!strcmp($this->inputs[$input]["TYPE"],"custom"))
					{
						if(strlen($error=$this->inputs[$input]["object"]->AddFunction($this, $arguments)))
							$this->OutputError($error,$input);
						return($error);
					}
					if(IsSet($this->inputs[$input]["InputElement"])
					&& strcmp($this->inputs[$input]["InputElement"],""))
					{
						$function["Element"]=$input;
						break;
					}
				}
				return($this->OutputError("it was not specified a valid named form element to define a function",$name));
			case "void":
				break;
			default:
				return($this->OutputError("it was not specified a valid function type",$name));
		}
		$this->functions[$name]=$function;
		return("");
	}

	Function RemoveFunction($name)
	{
		if(!IsSet($this->functions[$name]))
			return($this->OutputError("it was not specified an existing function name",$name));
		Unset($this->functions[$name]);
		return("");
	}

	Function OutputOnChangeAttribute(&$input,$function)
	{
		$onchange="";
		if(IsSet($input["Capitalization"]))
		{
			switch($input["Capitalization"])
			{
				case "uppercase":
					$onchange.="if(new_value.toUpperCase) new_value=new_value.toUpperCase() ; ";
					break;
				case "lowercase":
					$onchange.="if(new_value.toLowerCase) new_value=new_value.toLowerCase() ; ";
					break;
				case "words":
					$onchange.="if(new_value.toLowerCase && new_value.toUpperCase) { for(var capitalize=true, position=0, capitalized_value='' ; position<new_value.length; position++) { character=new_value.charAt(position) ; if(character==' ' || character=='\\t' || character=='\\n' || character=='\\r') { capitalize=true } else { character=(capitalize ? character.toUpperCase() : character.toLowerCase()) ; capitalize=false } ; capitalized_value+=character } new_value=capitalized_value } ; ";
					break;
			}
		}
		if(IsSet($input["ReplacePatterns"])
		&& count($input["ReplacePatterns"]))
		{
			for($value="new_value",$pattern=0,Reset($input["ReplacePatterns"]);$pattern<count($input["ReplacePatterns"]);Next($input["ReplacePatterns"]),$pattern++)
			{
				$expression=Key($input["ReplacePatterns"]);
				$replacement=ereg_replace('\\\\([0-9])','$\\1',$input["ReplacePatterns"][$expression]);
				$value=$value.".replace(new RegExp(\"".$this->EscapeJavascriptRegularExpressions($expression)."\",\"g\"), \"".$replacement."\")";
			}
			$onchange.="if(new_value.replace) { new_value=".$this->EncodeHTMLString($value)."; } ; ";
		}
		if(strlen($onchange))
			$onchange="new_value=value; ".$onchange." if(new_value!=value) value=new_value ;";
		if(IsSet($input["ONCHANGE"]))
			$onchange.=$input["ONCHANGE"];
		if(strcmp($onchange,""))
			$function(" onchange=\"$onchange\"");
	}

	Function OutputStyleAttributes(&$input,$function)
	{
		if(IsSet($input["CLASS"]))
			$function(" class=\"".$this->EncodeHTMLString($input["CLASS"])."\"");
		if(IsSet($input["STYLE"]))
			$function(" style=\"".$this->EncodeHTMLString($input["STYLE"])."\"");
		if(IsSet($input["BORDER"]))
			$function(" border=\"".intval($input["border"])."\"");
	}

	Function OutputExtraAttributes(&$attributes,$function)
	{
		if(IsSet($attributes))
		{
			for(Reset($attributes),$attribute=0;$attribute<count($attributes);Next($attributes),$attribute++)
			{
				$attribute_name=Key($attributes);
				$function(" $attribute_name=\"".$this->EncodeHTMLString($attributes[$attribute_name])."\"");
			}
		}
	}

	Function OutputInput(&$input, $input_id, $input_read_only, $function, $eol, &$resubmit_condition)
	{
		switch($input["TYPE"])
		{
			case "textarea":
				if(!$input_read_only)
				{
					$function("<textarea name=\"".$input["NAME"]."\"");
					if(IsSet($input["ROWS"]))
						$function(" rows=\"".$input["ROWS"]."\"");
					if(IsSet($input["COLS"]))
						$function(" cols=\"".$input["COLS"]."\"");
					$this->OutputOnChangeAttribute($input,$function);
					if(IsSet($input["ID"]))
						$function(" id=\"".$input["ID"]."\"");
					if(IsSet($input["TABINDEX"])
					|| strcmp($tab_index_function=$this->accessibility_tab_index,""))
						$function(" tabindex=\"".(IsSet($input["TABINDEX"]) ? $input["TABINDEX"] : $tab_index_function($input_id))."\"");
					if(IsSet($input["ACCESSKEY"]))
					$function(" accesskey=\"".$input["ACCESSKEY"]."\"");
					$this->OutputStyleAttributes($input,$function);
					$this->OutputExtraAttributes($input["ExtraAttributes"],$function);
					$function(">");
				}
				if(IsSet($input["VALUE"]))
					$function($this->EncodeHTMLString($input["VALUE"]));
				if(!$input_read_only)
					$function("</textarea>");
				break;
			case "select":
				if(!$input_read_only)
				{
					$function("<select name=\"".$input["NAME"]);
					if(IsSet($input["MULTIPLE"]))
						$function("[]\" multiple=\"multiple\"");
					else
						$function("\"");
					$this->OutputOnChangeAttribute($input,$function);
					if(IsSet($input["ID"]))
						$function(" id=\"".$input["ID"]."\"");
					if(IsSet($input["SIZE"]))
						$function(" size=\"".$input["SIZE"]."\"");
					if(IsSet($input["TABINDEX"])
					|| strcmp($tab_index_function=$this->accessibility_tab_index,""))
						$function(" tabindex=\"".(IsSet($input["TABINDEX"]) ? $input["TABINDEX"] : $tab_index_function($input_id))."\"");
					if(IsSet($input["ACCESSKEY"]))
					$function(" accesskey=\"".$input["ACCESSKEY"]."\"");
					$this->OutputStyleAttributes($input,$function);
					$this->OutputExtraAttributes($input["ExtraAttributes"],$function);
					$function(">$eol");
				}
				$options=$input["OPTIONS"];
				for($space="",$option=0,Reset($options);$option<count($options);Next($options),$option++)
				{
					if(!$input_read_only)
						$function("<option value=\"".$this->EncodeHTMLString(Key($options))."\"");
					if(IsSet($input["MULTIPLE"]))
					{
						if(IsSet($input["SELECTED"][Key($options)]))
						{
							$function($input_read_only ? $space.$this->EncodeHTMLString($options[Key($options)]) : " selected=\"selected\"");
							if($input_read_only)
								$space=$this->OptionsSeparator;
						}
					}
					else
					{
						if(IsSet($input["VALUE"])
						&& !strcmp($input["VALUE"],Key($options)))
							$function($input_read_only ? $this->EncodeHTMLString($options[Key($options)]) : " selected=\"selected\"");
					}
					if(!$input_read_only)
						$function(">".$this->EncodeHTMLString($options[Key($options)])."</option>$eol");
				}
				if(!$input_read_only)
					$function("</select>");
				break;
			case "custom":
				break;
			default:
				if($input_read_only)
				{
					switch($input["TYPE"])
					{
						case "hidden":
							break;
						case "submit":
						case "image":
						case "reset":
						case "button":
							if(IsSet($input["ReadOnlyMark"]))
								$function($input["ReadOnlyMark"]);
							break;
						case "checkbox":
						case "radio":
							if(IsSet($input["CHECKED"]))
								$function(IsSet($input["ReadOnlyMark"]) ? $input["ReadOnlyMark"] : (IsSet($input["VALUE"]) ? $this->EncodeHTMLString($input["VALUE"]) : "On"));
							break;
						default:
							if(IsSet($input["VALUE"]))
								$function(IsSet($input["ReadOnlyMark"]) ? $input["ReadOnlyMark"] : $this->EncodeHTMLString($input["VALUE"]));
							break;
					}
				}
				else
				{
					$function("<input type=\"".$input["TYPE"]."\"");
					if(IsSet($input["NAME"]))
					{
						$name=$input["NAME"];
						if(!strcmp($input["TYPE"],"checkbox")
						&& IsSet($input["MULTIPLE"]))
							$name.="[]";
						$function(" name=\"".$this->EncodeHTMLString($name)."\"");
					}
					$accessible_input=0;
					$onclick="";
					switch($input["TYPE"])
					{
						case "password":
						case "text":
							if(($input["TYPE"]!="password"
							|| $this->OutputPasswordValues)
							&& IsSet($input["VALUE"]))
								$function(" value=\"".$this->EncodeHTMLString($input["VALUE"])."\"");
						case "file":
							if(IsSet($input["ACCEPT"]))
								$function(" accept=\"".$input["ACCEPT"]."\"");
							if(IsSet($input["MAXLENGTH"]))
								$function(" maxlength=\"".$input["MAXLENGTH"]."\"");
							if(IsSet($input["SIZE"]))
								$function(" size=\"".$input["SIZE"]."\"");
							$accessible_input=1;
							break;
						case "checkbox":
						case "radio":
							if(IsSet($input["VALUE"]))
								$function(" value=\"".$this->EncodeHTMLString($input["VALUE"])."\"");
							if(IsSet($input["CHECKED"]))
								$function(" checked=\"checked\"");
							$accessible_input=1;
							break;
						case "image":
							$function(" src=\"".$input["SRC"]."\"");
							if(IsSet($input["ALT"]))
								$function(" alt=\"".$this->EncodeHTMLString($input["ALT"])."\"");
						case "submit":
							if(strcmp($resubmit_condition,""))
								$onclick="if(this.disabled || typeof(this.disabled)=='boolean') this.disabled=true ; ".$this->form_submitted_test_variable_name."=".$this->form_submitted_variable_name." ; ".$this->form_submitted_variable_name."=true ; ".$this->form_submitted_variable_name."=".$resubmit_condition." ; if(this.disabled || typeof(this.disabled)=='boolean') this.disabled=false";
							if(IsSet($input["SubForm"]))
							{
								if(strcmp($onclick,""))
									$onclick.=" ; ";
								if($this->client_validate)
									$onclick.=$this->sub_form_variable_name."='".$input["SubForm"]."'";
							}
						case "reset":
						case "button":
							$accessible_input=1;
							if(IsSet($input["VALUE"]))
								$function(" value=\"".$input["VALUE"]."\"");
							break;
						case "hidden":
							$function(" value=\"".(IsSet($input["VALUE"]) ? $this->EncodeHTMLString($input["VALUE"]) : "")."\"");
							break;
					}
					$this->OutputOnChangeAttribute($input,$function);
					if(IsSet($input["ONCLICK"]))
					{
						if(strcmp($onclick,""))
							$onclick.=" ; ";
						$onclick.=$input["ONCLICK"];
					}
					if(strcmp($onclick,""))
						$function(" onclick=\"$onclick ; return true\"");
					if(IsSet($input["ID"]))
						$function(" id=\"".$input["ID"]."\"");
					if($accessible_input
					&& (IsSet($input["TABINDEX"])
					|| strcmp($tab_index_function=$this->accessibility_tab_index,"")))
						$function(" tabindex=\"".(IsSet($input["TABINDEX"]) ? $input["TABINDEX"] : $tab_index_function($input_id))."\"");
					if(strcmp($input["TYPE"],"hidden")
					&& IsSet($input["ACCESSKEY"]))
					$function(" accesskey=\"".$input["ACCESSKEY"]."\"");
					$this->OutputStyleAttributes($input,$function);
					$this->OutputExtraAttributes($input["ExtraAttributes"],$function);
					$function(" />");
				}
				break;
		}
	}

	Function Output($arguments)
	{
		$function="Output";
		if(!IsSet($arguments["Function"])
		|| !strcmp($function=$arguments["Function"],""))
			return($this->OutputError("it was not specified a valid output function",$function));
		if($this->client_validate
		&& !strcmp($this->ValidationFunctionName,""))
			return($this->OutputError("it was not specified a valid client validation function name","Output"));
		if(($this->client_validate
		|| count($this->functions))
		&& !strcmp($this->NAME,""))
			return($this->OutputError("it was not specified a valid form name","Output"));
		$eol=(IsSet($arguments["EndOfLine"]) ? $arguments["EndOfLine"] : "");
		$resubmit_condition="";
		if(!$this->ReadOnly
		|| $this->hidden_parts
		|| $this->accessible_parts)
		{
			$function("<form method=\"$this->METHOD\" action=\"$this->ACTION\"");
			if(strcmp($this->NAME,""))
				$function(" name=\"$this->NAME\"");
			if(strcmp($this->TARGET,""))
				$function(" target=\"$this->TARGET\"");
			if(strcmp($this->ENCTYPE,""))
				$function(" enctype=\"$this->ENCTYPE\"");
			if(strcmp($this->form_submitted_variable_name,"")
			&& strcmp($this->form_submitted_test_variable_name,"")
			&& strcmp($this->ResubmitConfirmMessage,""))
				$resubmit_condition="(!".$this->form_submitted_test_variable_name." || confirm(".$this->EncodeJavascriptString($this->ResubmitConfirmMessage)."))";
			if(strcmp($this->ONSUBMITTING,""))
				$onsubmit=($this->client_validate ? "if(".$this->ValidationFunctionName."(this)==false) return false; " : "").$this->ONSUBMITTING."; return true";
			else
				$onsubmit=($this->client_validate ? "return ".$this->ValidationFunctionName."(this)" : "");
			if(strcmp($this->ONSUBMIT,""))
				$onsubmit=$this->ONSUBMIT."; $onsubmit";
			if(strcmp($onsubmit,""))
				$function(" onsubmit=\"".$this->EncodeHTMLString($onsubmit)."\"");
			if(strcmp($this->ONRESET,""))
				$function(" onreset=\"".$this->EncodeHTMLString($this->ONRESET)."\"");
			$this->OutputExtraAttributes($this->ExtraAttributes,$function);
			$function(">$eol");
		}
		if($this->client_validate
		|| strcmp($resubmit_condition,"")
		|| count($this->functions))
		{
			$jseol=(!strcmp($eol,"") ? "\n" : $eol);
			if(count($this->parts)
			&& $this->client_validate)
			{
				for($script_files=$password_fields=array(),$input_part=0;$input_part<count($this->input_parts);$input_part++)
				{
					$input=$this->inputs[$this->input_parts[$input_part]];
					$part_type=$this->types[$input["Part"]];
					if(!strcmp($input["TYPE"],"password")
					&& IsSet($input["Encoding"])
					&& (!strcmp($part_type,"INPUT")
					|| !strcmp($part_type,"ACCESSIBLE_INPUT")))
					{
						$password_fields[]=$this->input_parts[$input_part];
						if(IsSet($input["EncodingFunctionScriptFile"])
						&& !IsSet($script_files[$input["EncodingFunctionScriptFile"]]))
						{
							$function("<script type=\"text/javascript\" defer=\"defer\" src=\"".$input["EncodingFunctionScriptFile"]."\">\n</script>\n");
							$script_files[$input["EncodingFunctionScriptFile"]]=$this->input_parts[$input_part];
						}
					}
				}
			}
			$function("<script type=\"text/javascript\" defer=\"defer\">$eol<!--$jseol");
			if(strcmp($resubmit_condition,""))
				$function($this->form_submitted_variable_name."=false$jseol");
			if(count($this->parts)
			&& $this->client_validate)
			{
				for($needs_sub_form=0,$validate_as_email="",$validate_as_credit_card="",$part=0;$part<count($this->parts);$part++)
				{
					switch($this->types[$part])
					{
						case "INPUT":
						case "ACCESSIBLE_INPUT":
							if(IsSet($this->inputs[$this->parts[$part]]["ClientScript"]))
								$function($jseol.$this->inputs[$this->parts[$part]]["ClientScript"].$jseol);
							break;
					}
				}
				for($validate_as_email="",$validate_as_credit_card="",$part=0;$part<count($this->parts);$part++)
				{
					switch($this->types[$part])
					{
						case "INPUT":
						case "ACCESSIBLE_INPUT":
							$input=$this->inputs[$this->parts[$part]];
							if($input["ClientValidate"])
							{
								if(strcmp($input["SubForm"],""))
									$needs_sub_form=1;
								If(IsSet($input["ValidateAsEmail"]))
								{
									if(!strcmp($validate_as_email,""))
									{
										if(!IsSet($this->ValidateAsEmail)
										|| !strcmp($this->ValidateAsEmail,""))
											return($this->OutputError("it was not specified a valid validation as email function name","Output"));
										$validate_as_email=$this->ValidateAsEmail;
										$function("function $validate_as_email(theinput)$jseol{".$jseol);
										$function("\ts=theinput.value$jseol\tif(s.search)$jseol\t{"."$jseol\t\treturn (s.search(new RegExp(\"".$this->EscapeJavascriptRegularExpressions($this->email_regular_expression)."\",\"gi\"))>=0)$jseol\t}$jseol");
										$function("\tif(s.indexOf)$jseol\t{"."$jseol\t\tat_character=s.indexOf('@')$jseol\t\tif(at_character<=0 || at_character+4>s.length)$jseol\t\t\treturn false$jseol\t}$jseol");
										$function("\tif(s.length<6)$jseol\t\treturn false$jseol\telse$jseol\t\treturn true$jseol}$jseol");
									}
								}
								If(IsSet($input["ValidateAsCreditCard"]))
								{
									if(!strcmp($validate_as_credit_card,""))
									{
										if(!IsSet($this->ValidateAsCreditCard)
										|| !strcmp($this->ValidateAsCreditCard,""))
											return($this->OutputError("it was not specified a valid validation as credit card function name","Output"));
										$validate_as_credit_card=$this->ValidateAsCreditCard;
										$function("function $validate_as_credit_card(theinput,cardtype)$jseol{"."$jseol");
										$function("\tval=theinput.value$jseol\tlen=val.length$jseol\tfor(position=0;position<len;)$jseol\t{"."$jseol\t\tif(val.charAt(position)==' ' || val.charAt(position)=='.' || val.charAt(position)=='-')$jseol\t\t{"."$jseol$jseol\t\t\tval=val.substring(0,position)+val.substring(position+1,len)$jseol\t\t\tlen--$jseol\t\t}$jseol\t\telse$jseol\t\t\tposition++$jseol\t}$jseol\tif(len<13)$jseol\t\treturn false$jseol");
										$function("\tif(cardtype!='unknown')$jseol\t{".$jseol);
										$function("\t\tif(isNaN(first=parseInt(val.charAt(0),10)))$jseol\t\t\treturn false$jseol");
										$function("\t\tif(isNaN(second=parseInt(val.charAt(1),10)))$jseol\t\t\treturn false$jseol");
										$function("\t\tif(isNaN(third=parseInt(val.charAt(2),10)))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='mastercard') && (len!=16 || first!=5 || second<1 || second>5))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='visa') && ((len!=16 && len!=13) || first!=4))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='amex') && (len!=15 || first!=3 || (second!=4 && second!=7)))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='dinersclub' || cardtype=='carteblanche') && (len!=14 || first!=3 || ((second!=0 || third<0 || third>5) && second!=6 && second!=8)))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='discover') && (len!=16 || first!=5 || second<1 || second>5))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='enroute') && (len!=15 || (val.substring(0,4)!='2014' && val.substring(0,4)!='2149')))$jseol\t\t\treturn false$jseol");
										$function("\t\tif((cardtype=='jcb') && ((len!=16 || first!=3) && (len!=15 || (val.substring(0,4)!='2031' && val.substring(0,4)!='1800'))))$jseol\t\t\treturn false$jseol");
										$function("\t}$jseol");
										$function("\tfor(check=0,position=1;position<=len;position++)$jseol\t{".$jseol);
										$function("\t\tif(isNaN(digit=parseInt(val.charAt(len-position),10)))$jseol\t\t\treturn false$jseol");
										$function("\t\tif(!(position % 2))$jseol\t\t\tdigit=parseInt('0246813579'.charAt(digit),10)$jseol\t\tcheck+=digit$jseol");
										$function("\t}$jseol\treturn((check % 10)==0)$jseol}$jseol");
									}
								}
							}
							else
							{
								if((!strcmp($input["TYPE"],"submit")
								|| !strcmp($input["TYPE"],"image"))
								&& strcmp($input["SubForm"],""))
									$needs_sub_form=1;
							}
							break;
					}
				}
				if($needs_sub_form)
					$function($jseol.$this->sub_form_variable_name."=''$jseol$jseol");
				$function("function $this->ValidationFunctionName(theform)$eol{".$eol);
				for($custom=array(),$input_part=0;$input_part<count($this->input_parts);$input_part++)
				{
					$input_id=$this->input_parts[$input_part];
					if(!strcmp($this->inputs[$input_id]["TYPE"],"custom"))
					{
						$custom[]=$input_id;
						continue;
					}
					$input=$this->inputs[$input_id];
					$part_type=$this->types[$input["Part"]];
					if($input["ClientValidate"]
					&& (!strcmp($part_type,"INPUT")
					|| !strcmp($part_type,"ACCESSIBLE_INPUT")))
					{
						$validations=array("ValidateAsNotEmpty","ValidateMinimumLength","ValidateRegularExpression","ValidateAsInteger","ValidateAsFloat","ValidateAsEmail","ValidateAsCreditCard","ValidateAsEqualTo","ValidateAsDifferentFrom","ValidateAsSet","ValidationClientFunction");
						for($last_optional_value_set=0,$last_optional_value=$last_field_value=$last_test=$last_subform=$last_error_message="",$last_field="",$validation=0,$validate_as_set=array();$validation<count($validations);$validation++)
						{
							if(IsSet($input[$validations[$validation]]))
							{
								$error_message=(IsSet($input["ValidationErrorMessage"]) ? $input["ValidationErrorMessage"] : "");
								$field=$this->GetJavascriptInputObject("theform", $this->input_parts[$input_part]);
								$field_value=$this->GetJavascriptInputValue("theform", $this->input_parts[$input_part]);
								switch($validations[$validation])
								{
									case "ValidateRegularExpression":
										$expression=$this->EscapeJavascriptRegularExpressions($input["ValidateRegularExpression"]);
										$test="(".$field.".value.search$jseol\t&& ".$field.".value.search(new RegExp(\"$expression\",\"g\"))<0)";
										if(IsSet($input["ValidateRegularExpressionErrorMessage"]))
											$error_message=$input["ValidateRegularExpressionErrorMessage"];
										break;
									case "ValidateAsNotEmpty":
										if($input["TYPE"]=="select")
											$test=$field.".selectedIndex==-1 || ".$field_value."==''";
										else
											$test=$field_value."==''";
										if(IsSet($input["ValidateAsNotEmptyErrorMessage"]))
											$error_message=$input["ValidateAsNotEmptyErrorMessage"];
										break;
									case "ValidateMinimumLength":
										if($input["TYPE"]=="select")
											$test=$field.".selectedIndex==-1 || ".$field_value.".length<".$input["ValidateMinimumLength"];
										else
											$test=$field_value.".length<".$input["ValidateMinimumLength"];
										if(IsSet($input["ValidateMinimumLengthErrorMessage"]))
											$error_message=$input["ValidateMinimumLengthErrorMessage"];
										break;
									case "ValidateAsEqualTo":
										$test=$field_value."!=".$this->GetJavascriptInputValue("theform", $input["ValidateAsEqualTo"]);
										if(IsSet($input["ValidateAsEqualToErrorMessage"]))
											$error_message=$input["ValidateAsEqualToErrorMessage"];
										break;
									case "ValidateAsDifferentFrom":
										$test=$field_value."==".$this->GetJavascriptInputValue("theform", $input["ValidateAsDifferentFrom"]);
										if(IsSet($input["ValidateAsDifferentFromErrorMessage"]))
											$error_message=$input["ValidateAsDifferentFromErrorMessage"];
										break;
									case "ValidateAsSet":
										switch($input["TYPE"])
										{
											case "radio":
												for($test="",$set_part=$validate_input=0;$validate_input<count($this->input_parts);$validate_input++)
												{
													$set_input=$this->inputs[$this->input_parts[$validate_input]];
													if(!strcmp($set_input["TYPE"],"radio")
													&& IsSet($set_input["NAME"])
													&& !strcmp($set_input["NAME"],$input["NAME"]))
													{
														if($set_part>0)
															$test.=" && ";
														$test.=$this->GetJavascriptInputObject("theform", $this->input_parts[$validate_input]).".checked==false";
														$set_part++;
													}
												}
												break;
											case "checkbox":
												if(IsSet($input["MULTIPLE"]))
												{
													for($test="",$set_part=$validate_input=0;$validate_input<count($this->input_parts);$validate_input++)
													{
														$id=$this->input_parts[$validate_input];
														$set_input=$this->inputs[$id];
														if(!strcmp($set_input["TYPE"],"checkbox")
														&& IsSet($set_input["MULTIPLE"])
														&& IsSet($set_input["NAME"])
														&& !strcmp($set_input["NAME"],$input["NAME"]))
														{
															if($set_part>0)
																$test.=" && ";
															$test.=$this->GetJavascriptInputObject("theform", $id).".checked==false";
															$set_part++;
														}
													}
												}
												else
													$test=$field.".checked==false";
												break;
											case "select":
												if(IsSet($input["MULTIPLE"]))
													$test=$field.".selectedIndex==-1";
												break;
										}
										if(IsSet($input["ValidateAsSetErrorMessage"]))
											$error_message=$input["ValidateAsSetErrorMessage"];
										break;
									case "ValidateAsInteger":
										$test="isNaN(parseInt(".$field_value.",10))";
										if(IsSet($input["ValidationLowerLimit"]))
											$test.="$jseol\t|| parseInt(".$field_value.",10) < ".$input["ValidationLowerLimit"];
										if(IsSet($input["ValidationUpperLimit"]))
											$test.="$jseol\t|| ".$input["ValidationUpperLimit"]." < parseInt(".$field_value.",10)";
										if(IsSet($input["ValidateAsIntegerErrorMessage"]))
											$error_message=$input["ValidateAsIntegerErrorMessage"];
										break;
									case "ValidateAsFloat":
										$test=(IsSet($input["ValidationDecimalPlaces"]) ? "(".$field_value.".search".$jseol."\t&& ".$field_value.".search(new RegExp(\"^[0-9]+(\\\\.[0-9]{0,".$input["ValidationDecimalPlaces"]."})?\$\",\"g\"))<0)".$jseol."\t|| " : "")."isNaN(parseFloat(".$field_value."))";
										if(IsSet($input["ValidationLowerLimit"]))
											$test.="$jseol\t|| parseFloat(".$field_value.") < ".$input["ValidationLowerLimit"];
										if(IsSet($input["ValidationUpperLimit"]))
											$test.="$jseol\t|| ".$input["ValidationUpperLimit"]." < parseFloat(".$field_value.")";
										if(IsSet($input["ValidateAsFloatErrorMessage"]))
											$error_message=$input["ValidateAsFloatErrorMessage"];
										break;
									case "ValidateAsCreditCard":
										if(strcmp($input["ValidateAsCreditCard"],"field"))
											$card_type=$this->EncodeJavascriptString($input["ValidateAsCreditCard"]);
										else
										{
											$card_type_field=$input["ValidationCreditCardTypeField"];
											$card_type=$this->GetJavascriptInputValue("theform", $card_type_field);
										}
										$test=$validate_as_credit_card."(".$field.",$card_type)==false";
										if(IsSet($input["ValidateAsCreditCardErrorMessage"]))
											$error_message=$input["ValidateAsCreditCardErrorMessage"];
										break;
									case "ValidateAsEmail":
										$test=$validate_as_email."(".$field.")==false";
										if(IsSet($input["ValidateAsEmailErrorMessage"]))
											$error_message=$input["ValidateAsEmailErrorMessage"];
										break;
									case "ValidationClientFunction":
										$test=$input["ValidationClientFunction"]."(".$field.")==false";
										if(IsSet($input["ValidationClientFunctionErrorMessage"]))
											$error_message=$input["ValidationClientFunctionErrorMessage"];
										break;
								}
								$subform=(strcmp($input["SubForm"],"") ? $input["SubForm"] : "");
								$optional_value_set=IsSet($input["ValidateOptionalValue"]);
								$optional_value=($optional_value_set ? $input["ValidateOptionalValue"] : "");
								if(strcmp($last_error_message,$error_message)
								|| strcmp($last_field,$field))
								{
									if(strcmp($last_test,""))
									{
										if($last_optional_value_set)
											$last_test=$last_field_value."!=".$this->EncodeJavascriptString($last_optional_value).$jseol."\t&& (".$last_test.")";
										if($needs_sub_form
										|| strcmp($last_subform,""))
										{
											$sub_form_test=$this->sub_form_variable_name."=='$last_subform'";
											if(strcmp($last_subform,""))
												$sub_form_test="(".$this->sub_form_variable_name."==''".$jseol."\t|| $sub_form_test)";
											$last_test="$sub_form_test$jseol\t&& ($last_test)";
										}
										$function($last_test);
										$function(")$jseol\t{".$jseol);
										$function("\t\tif(".$field.".focus)".$jseol);
										$function("\t\t\t".$field.".focus()".$jseol);
										$function("\t\talert(".$this->EncodeJavascriptString($last_error_message).")$jseol");
										if(strcmp($resubmit_condition,""))
											$function("\t\t".$this->form_submitted_variable_name."=false$jseol");
										$function("\t\treturn false$jseol\t}$jseol");
										$last_test=$last_subform="";
									}
									$function("\tif(");
									$last_error_message=$error_message;
									$last_field=$field;
									$conditions=0;
								}
								else
								{
									if($conditions>0)
										$test=$jseol."\t|| ".$test;
								}
								$conditions++;
								$last_test.=$test;
								$last_subform=$subform;
								$last_optional_value_set=$optional_value_set;
								$last_optional_value=$optional_value;
								$last_field_value=$field_value;
							}
						}
						if(strcmp($last_test,""))
						{
							if($last_optional_value_set)
								$last_test=$last_field_value."!=".$this->EncodeJavascriptString($last_optional_value).$jseol."\t&& (".$last_test.")";
							if($needs_sub_form
							|| strcmp($last_subform,""))
							{
								$sub_form_test=$this->sub_form_variable_name."=='$last_subform'";
								if(strcmp($last_subform,""))
									$sub_form_test="(".$this->sub_form_variable_name."==''".$jseol."\t|| $sub_form_test)";
								$last_test="$sub_form_test$jseol\t&& ($last_test)";
							}
							$function($last_test);
							$function(")$jseol\t{".$jseol);
							$function("\t\tif(".$field.".focus)".$jseol);
							$function("\t\t\t".$field.".focus()".$jseol);
							$function("\t\talert(".$this->EncodeJavascriptString($last_error_message).")$jseol");
							if(strcmp($resubmit_condition,""))
								$function("\t\t".$this->form_submitted_variable_name."=false$jseol");
							$function("\t\treturn false$jseol\t}$jseol");
						}
					}
				}
				for($input_part=0;$input_part<count($custom);$input_part++)
				{
					$input=$custom[$input_part];
					if(strlen($error=$this->inputs[$input]["object"]->GetJavascriptValidations($this, "theform", $validations)))
					{
						$this->OutputError($error,$input);
						return($error);
					}
					for($validation=0;$validation<count($validations);$validation++)
					{
						if(IsSet($validations[$validation]["Commands"]))
						{
							for($command=0; $command<count($validations[$validation]["Commands"]); $command++)
								$function("\t".$validations[$validation]["Commands"][$command].$jseol);
						}
						$function("\tif(".$validations[$validation]["Condition"].")".$jseol."\t{".$jseol);
						$focus=(IsSet($validations[$validation]["Focus"]) ? $validations[$validation]["Focus"] : $this->inputs[$input]["object"]->focus_input);
						if(strlen($focus))
						{
							$field=$this->GetJavascriptInputObject("theform",$focus);
							$function("\t\tif(".$field.".focus)".$jseol);
							$function("\t\t\t".$field.".focus()".$jseol);
						}
						$function("\t\talert(".$this->EncodeJavascriptString($validations[$validation]["ErrorMessage"]).")".$jseol);
						if(strcmp($resubmit_condition,""))
							$function("\t\t".$this->form_submitted_variable_name."=false".$jseol);
						$function("\t\treturn false".$jseol."\t}".$jseol);
					}
				}
				for($input_part=0;$input_part<count($password_fields);$input_part++)
				{
					$input=$this->inputs[$password_fields[$input_part]];
					$element_name="theform.".$input["InputElement"].".value";
					$function("\tif(".$input["EncodingFunctionVerification"].")\n");
					if(IsSet($input["EncodedField"]))
					{
						$encoded_field=$input["EncodedField"];
						if(!IsSet($this->inputs[$encoded_field]))
							return($this->OutputError("it was not defined the specified password encoded field",$encoded_field));
						if(!IsSet($this->inputs[$encoded_field]["InputElement"]))
							return($this->OutputError("it was not added the specified password encoded field",$encoded_field));
						if(!strcmp($this->inputs[$encoded_field]["InputElement"],""))
							return($this->OutputError("it was specified an unnamed password encoded field",$encoded_field));
						$function("\t{\n\t\ttheform.".$this->inputs[$encoded_field]["InputElement"].".value=".$input["Encoding"]."($element_name)\n\t\t$element_name=''\n\t}\n");
					}
					else
						$function("\t{\n\t\t$element_name=".$input["Encoding"]."($element_name)\n\t}\n");
				}
				$function("\treturn true$jseol}$jseol");
			}
			if(count($this->functions))
			{
				for($function_number=0,Reset($this->functions);$function_number<count($this->functions);Next($this->functions),$function_number++)
				{
					$function($jseol."function ".Key($this->functions)."()$jseol{".$jseol);
					$function_data=$this->functions[Key($this->functions)];
					if(strcmp($function_data["Type"],"void"))
					{
						$element="document.".$this->NAME."[".$this->EncodeJavascriptString($function_data["Element"])."]";
						switch($this->inputs[$function_data["Element"]]["TYPE"])
						{
							case "submit":
							case "image":
							case "reset":
							case "button":
								$test_function=1;
								break;
							default:
								$test_function=0;
								break;
						}
						switch($function_data["Type"])
						{
							case "focus":
								if($test_function)
									$function("\tif($element.focus)$jseol");
								$function("\t\t$element.focus()$jseol");
								break;
							case "select":
								if($test_function)
									$function("\tif($element.select)$jseol");
								$function("\t\t$element.select()$jseol");
								break;
							case "select_focus":
								if($test_function)
									$function("\tif($element.focus)$jseol");
								$function("\t\t$element.focus()$jseol");
								if($test_function)
									$function("\tif($element.select)$jseol");
								$function("\t\t$element.select()$jseol");
								break;
							case "disable":
							case "enable":
								$function("\tif($element.disabled$jseol\t|| typeof($element.disabled)=='boolean')$jseol");
								$function("\t\t$element.disabled=".($function_data["Type"]=="disable" ? "true" : "false")."$jseol");
								break;
							default:
								return($this->OutputError("function of type \"".$function_data["Type"]."\"not yet supported",Key($this->functions)));
						}
					}
					$function("}$jseol");
				}
			}
			$function("//-->$eol</script>$eol<noscript>$eol<!-- dummy comment for user agents without Javascript support enabled -->$eol</noscript>$eol");
		}
		for($part=0;$part<count($this->parts);$part++)
		{
			switch($this->types[$part])
			{
				case "DATA":
					$function($this->parts[$part]);
					break;
				case "ACCESSIBLE_INPUT":
				case "READ_ONLY_INPUT":
				case "INPUT":
					switch($this->types[$part])
					{
						case "ACCESSIBLE_INPUT":
							$input_read_only=0;
							break;
						case "READ_ONLY_INPUT":
							$input_read_only=1;
							break;
						case "INPUT":
							$input_read_only=$this->ReadOnly;
							break;
					}
					$this->OutputInput($this->inputs[$this->parts[$part]], $this->parts[$part], $input_read_only, $function, $eol, $resubmit_condition);
					break;
				case "HIDDEN_INPUT":
					$input=$this->inputs[$this->parts[$part]];
					Unset($value);
					switch($input["TYPE"])
					{
						case "textarea":
						case "text":
						case "select":
						case "submit":
						case "image":
						case "reset":
						case "button":
						case "hidden":
						case "password":
							$value=(IsSet($input["VALUE"]) ? $input["VALUE"] : "");
							break;
						case "checkbox":
						case "radio":
							if(IsSet($input["CHECKED"]))
								$value=(IsSet($input["VALUE"]) ? $input["VALUE"] : "on");
							break;
					}
					if(IsSet($value))
					{
						$function("<input type=\"hidden\"");
						if(IsSet($input["NAME"]))
							$function(" name=\"".$input["NAME"]."\"");
						$function(" value=\"".$this->EncodeHTMLString($value)."\" />");
					}
					break;
				case "LABEL":
					$label=$this->parts[$part];
				  if($this->ReadOnly
				  && (!IsSet($this->inputs[$label["FOR"]]["Part"])
				  || $this->types[$this->inputs[$label["FOR"]]["Part"]]!="ACCESSIBLE_INPUT"))
				  	$function($this->parts[$part]["LABEL"]);
				  else
						$function("<label for=\"".$label["FOR"]."\"".(IsSet($label["ACCESSKEY"]) ? " accesskey=\"".$label["ACCESSKEY"]."\"" : "").">".$label["LABEL"]."</label>");
					break;
			}
		}
		if(!$this->ReadOnly
		|| $this->hidden_parts
		|| $this->accessible_parts)
			$function("</form>$eol");
		return("");
	}

	Function IsSetGlobal($variable)
	{
		return(IsSet($GLOBALS[$variable]));
	}

	Function GetGlobal($variable)
	{
		return($GLOBALS[$variable]);
	}

	Function SetGlobal($variable,$value)
	{
		$GLOBALS[$variable]=$value;
	}

	Function IsSetValue($variable,$file)
	{
		global $HTTP_POST_FILES,$HTTP_POST_VARS,$HTTP_GET_VARS;

		return(($file ? IsSet($HTTP_POST_FILES[$variable]) : (IsSet($HTTP_GET_VARS[$variable]) || IsSet($HTTP_POST_VARS[$variable]))) || IsSet($GLOBALS[$variable]));
	}

	Function GetValue($variable,$file,$multiple=0)
	{
		global $HTTP_SERVER_VARS,$HTTP_POST_FILES,$HTTP_POST_VARS,$HTTP_GET_VARS;

		if($file)
		{
			if(IsSet($HTTP_POST_FILES[$variable]))
				return($HTTP_POST_FILES[$variable]["tmp_name"]);
		}
		switch(IsSet($HTTP_SERVER_VARS["REQUEST_METHOD"]) ? $HTTP_SERVER_VARS["REQUEST_METHOD"] : "")
		{
			case "POST":
				if(IsSet($HTTP_POST_VARS[$variable]))
				{
					$value=$HTTP_POST_VARS[$variable];
					break;
				}
			case "GET":
				if(IsSet($HTTP_GET_VARS[$variable]))
				{
					$value=$HTTP_GET_VARS[$variable];
					break;
				}
			default:
 				$value=(IsSet($GLOBALS[$variable]) ? $GLOBALS[$variable] : "");
				break;
		}
		if($multiple
		&& GetType($value)!="array")
				return(array());
		if(function_exists("ini_get")
		&& intval(ini_get("magic_quotes_gpc")))
		{
			if($multiple)
			{
				for($key=0;$key<count($value);$key++)
					$value[$key]=StripSlashes($value[$key]);
			}
			else
				$value=StripSlashes($value);
		}
		return($value);
	}

	Function SetValue($variable,$value)
	{
		global $HTTP_SERVER_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS;

		switch($HTTP_SERVER_VARS["REQUEST_METHOD"])
		{
			case "POST":
				if(IsSet($HTTP_POST_VARS[$variable]))
					$HTTP_POST_VARS[$variable]=$value;
				break;
			case "GET":
				if(IsSet($HTTP_GET_VARS[$variable]))
					$HTTP_GET_VARS[$variable]=$value;
				break;
		}
		if(IsSet($GLOBALS[$variable]))
			$GLOBALS[$variable]=$value;
		return("");
	}

	Function GetFileValues($input,&$values)
	{
		global $HTTP_POST_FILES;

		if($this->inputs[$input]
		&& $this->inputs[$input]["TYPE"]=="file")
		{
			$values=(IsSet($HTTP_POST_FILES[$input]) ? $HTTP_POST_FILES[$input] : ((IsSet($GLOBALS[$input]) && GetType($GLOBALS[$input])=="array") ? $GLOBALS[$input] : array()));
			if(IsSet($values["name"]))
				return($values["name"]);
		}
		$values=array();
		return("");
	}

	Function GetBooleanInputs()
	{
		for($this->checkbox_inputs=$this->radio_inputs=array(),$input_number=0,Reset($this->inputs);$input_number<count($this->inputs);Next($this->inputs),$input_number++)
		{
			$id=Key($this->inputs);
			switch($this->inputs[$id]["TYPE"])
			{
				case "radio":
					$this->radio_inputs[]=$id;
					break;
				case "checkbox":
					if(IsSet($this->inputs[$id]["MULTIPLE"]))
						$this->checkbox_inputs[]=$id;
					break;
			}
		}
	}

	Function ValidateInput($field, &$field_value, $sub_form="")
	{
		$input=$this->inputs[$field];
		$default_error=(IsSet($input["ValidationErrorMessage"]) ? $input["ValidationErrorMessage"] : "error");
		$input_error="";
		switch($input["TYPE"])
		{
			case "submit":
			case "image":
			case "reset":
			case "button":
				$value="";
				break;
			default:
				switch($input["TYPE"])
				{
					case "checkbox":
					case "radio":
						$value=(IsSet($field_value) ? $field_value : "on");
						break;
					default:
						$value=(IsSet($field_value) ? $field_value : "");
						break;
				}
/*
				$value=($this->IsSetValue($input["NAME"],$input["TYPE"]=="file") ? $value : "");
*/
				break;
		}
		if((!strcmp($input["TYPE"],"select")
		|| $input["ServerValidate"])
		&& ((!$this->ReadOnly
		&& !IsSet($input["Accessible"]))
		|| (IsSet($input["Accessible"])
		&& $input["Accessible"]))
		&& (!strcmp($sub_form,"")
		|| !strcmp($sub_form,$input["SubForm"])))
		{
			switch(GetType($value))
			{
				case "integer":
				case "double":
					$value=strval($value);
				case "string":
					break;
				default:
					$value="";
					break;
			}
			if(IsSet($input["ValidateOptionalValue"])
			&& !strcmp($input["ValidateOptionalValue"],$value))
				return("");
			$validations=array("ValidateAsNotEmpty","ValidateMinimumLength","ValidateRegularExpression","ValidateAsInteger","ValidateAsFloat","ValidateAsEmail","ValidateAsCreditCard","ValidateAsEqualTo","ValidateAsDifferentFrom","ValidateAsSet","ValidationServerFunction");
			for($validation=0;$validation<count($validations);$validation++)
			{
				if(IsSet($input[$validations[$validation]]))
				{
					switch($validations[$validation])
					{
						case "ValidateAsEmail":
							if(!eregi($this->email_regular_expression,$value))
								$input_error=(IsSet($input["ValidateAsEmailErrorMessage"]) ? $input["ValidateAsEmailErrorMessage"] : $default_error);
							break;
						case "ValidateAsCreditCard":
							$value=ereg_replace("[- .]","",$value);
							$len=strlen($value);
							$check=0;
							$input_error="";
							if(!strcmp($validation_type=$input["ValidateAsCreditCard"],"field"))
							{
								$type_field=$input["ValidationCreditCardTypeField"];
								$validation_type=($this->IsSetValue($type_field,0) ? $this->inputs[$type_field]["VALUE"] : "");
							}
							else
								$type_field="";
							if($check==0
							&& $len<13)
								$check=1;
							else
							{
								$first=Ord($value[0])-Ord("0");
								$second=Ord($value[1])-Ord("0");
								$third=Ord($value[2])-Ord("0");
								switch($validation_type)
								{
									case "mastercard":
										if($len!=16
										|| $first!=5
										|| $second<1
										|| $second>5)
											$check=1;
										break;
									case "visa":
										if(($len!=16
										&& $len!=13)
										|| $first!=4)
											$check=1;
										break;
									case "amex":
										if($len!=15
										|| $first!=3
										|| ($second!=4
										&& $second!=7))
											$check=1;
										break;
									case "carteblanche":
									case "dinersclub":
										if($len!=14
										|| $first!=3
										|| (($second!=0
										|| $third<0
										|| $third>5)
										&& $second!=6
										&& $second!=8))
											$check=1;
										break;
									case "discover":
										if($len!=16
										|| $first!=5
										|| $second<1
										|| $second>5)
											$check=1;
										break;
									case "enroute":
										if($len!=15
										|| (substr($value,0,4)!="2014"
										&& substr($value,0,4)!="2149"))
											$check=1;
										break;
									case "jcb":
										if(($len!=16
										|| $first!=3)
										&& ($len!=15
										|| (substr($value,0,4)!="2031"
										&& substr($value,0,4)!="1800")))
											$check=1;
										break;
									case "unknown":
										break;
									default:
										if(strcmp($type_field,""))
										{
											$type_input=$this->inputs[$type_field];
											$input_error=(IsSet($type_input["ValidationErrorMessage"]) ? $type_input["ValidationErrorMessage"] : "error");
										}
										$check=1;
										break;
								}
							}
							if($check==0)
							{
								for($odd="0246813579",$zero=Ord("0"),$position=1;$position<=$len;$position++)
								{
									if(($digit=Ord($value[$len-$position])-$zero)>9
									|| $digit<0)
									{
										$check=1;
										break;
									}
									if(!($position % 2))
										$digit=intval($odd[$digit]);
									$check+=$digit;
								}
								$check%=10;
							}
							if($check
							&& !strcmp($input_error,""))
								$input_error=(IsSet($input["ValidateAsCreditCardErrorMessage"]) ? $input["ValidateAsCreditCardErrorMessage"] : $default_error);
							break;
						case "ValidateRegularExpression":
							if(!ereg($input["ValidateRegularExpression"],$value))
								$input_error=(IsSet($input["ValidateRegularExpressionErrorMessage"]) ? $input["ValidateRegularExpressionErrorMessage"] : $default_error);
							break;
						case "ValidateAsNotEmpty":
							if($input["TYPE"]=="file" ? (strlen($this->GetFileValues($input["NAME"],$file_values))==0 || $file_values["size"]==0) : (strlen($value)==0))
								$input_error=(IsSet($input["ValidateAsNotEmptyErrorMessage"]) ? $input["ValidateAsNotEmptyErrorMessage"] : $default_error);
							break;
						case "ValidateMinimumLength":
							if(strlen($value)<$input["ValidateMinimumLength"])
								$input_error=(IsSet($input["ValidateMinimumLengthErrorMessage"]) ? $input["ValidateMinimumLengthErrorMessage"] : $default_error);
							break;
						case "ValidateAsEqualTo":
							if(!$this->IsSetValue($input["ValidateAsEqualTo"],0)
							|| strcmp($this->inputs[$input["ValidateAsEqualTo"]]["VALUE"],$value))
								$input_error=(IsSet($input["ValidateAsEqualToErrorMessage"]) ? $input["ValidateAsEqualToErrorMessage"] : $default_error);
							break;
						case "ValidateAsDifferentFrom":
							if(!$this->IsSetValue($input["ValidateAsDifferentFrom"],0)
							|| !strcmp($this->inputs[$input["ValidateAsDifferentFrom"]]["VALUE"],$value))
								$input_error=(IsSet($input["ValidateAsDifferentFromErrorMessage"]) ? $input["ValidateAsDifferentFromErrorMessage"] : $default_error);
							break;
						case "ValidateAsSet":
							$invalid=0;
							switch($input["TYPE"])
							{
								case "radio":
									if(IsSet($input["CHECKED"]))
										break;
									if(!IsSet($this->radio_inputs))
										$this->GetBooleanInputs();
									for($validate_input=0;$validate_input<count($this->radio_inputs);$validate_input++)
									{
										$set_input=$this->inputs[$this->radio_inputs[$validate_input]];
										if(IsSet($set_input["NAME"])
										&& !strcmp($set_input["NAME"],$input["NAME"])
										&& IsSet($set_input["CHECKED"]))
											break;
									}
									if($validate_input>=count($this->radio_inputs))
										$invalid=1;
									break;
								case "checkbox":
									if(IsSet($input["MULTIPLE"]))
									{
										if(IsSet($input["CHECKED"]))
											break;
										if(!IsSet($this->checkbox_inputs))
											$this->GetBooleanInputs();
										for($validate_input=0;$validate_input<count($this->checkbox_inputs);$validate_input++)
										{
											$set_input=$this->inputs[$this->checkbox_inputs[$validate_input]];
											if(IsSet($set_input["NAME"])
											&& !strcmp($set_input["NAME"],$input["NAME"])
											&& IsSet($set_input["CHECKED"]))
												break;
										}
										if($validate_input>=count($this->checkbox_inputs))
											$invalid=1;
										break;
									}
								default:
									if(!$this->IsSetValue($input["NAME"],0))
										$invalid=1;
									break;
							}
							if($invalid)
								$input_error=(IsSet($input["ValidateAsSetErrorMessage"]) ? $input["ValidateAsSetErrorMessage"] : $default_error);
							break;
						case "ValidateAsInteger":
							$integer_value=intval($value);
							if(strcmp($value,strval($integer_value))
							|| (IsSet($input["ValidationLowerLimit"])
							&& $integer_value<$input["ValidationLowerLimit"])
							|| (IsSet($input["ValidationUpperLimit"])
							&& $integer_value>$input["ValidationUpperLimit"]))
								$input_error=(IsSet($input["ValidateAsIntegerErrorMessage"]) ? $input["ValidateAsIntegerErrorMessage"] : $default_error);
							break;
						case "ValidateAsFloat":
							$float_value=doubleval($value);
							if(!ereg("^[+-]?[0-9]+(\\.[0-9]*)?([Ee][+-]?[0-9]+)?$",$value)
							|| (IsSet($input["ValidationLowerLimit"])
							&& $float_value<$input["ValidationLowerLimit"])
							|| (IsSet($input["ValidationUpperLimit"])
							&& $float_value>$input["ValidationUpperLimit"]))
								$input_error=(IsSet($input["ValidateAsFloatErrorMessage"]) ? $input["ValidateAsFloatErrorMessage"] : $default_error);
							break;
						case "ValidationServerFunction":
							if(!$input["ValidationServerFunction"]($value))
								$input_error=(IsSet($input["ValidationServerFunctionErrorMessage"]) ? $input["ValidationServerFunctionErrorMessage"] : $default_error);
							break;
					}
					if(strcmp($input_error,""))
						break;
				}
			}
			if(!strcmp($input_error,"")
			&& !strcmp($input["TYPE"],"select"))
			{
				if(IsSet($input["MULTIPLE"]))
				{
					if(IsSet($input["ValidateAsSet"])
					&& (!IsSet($input["SELECTED"])
					|| count($input["SELECTED"])==0))
						$input_error=(IsSet($input["ValidateAsSetErrorMessage"]) ? $input["ValidateAsSetErrorMessage"] : $default_error);
				}
				else
				{
					if($this->IsSetValue($input["NAME"],0)
					&& !IsSet($input["OPTIONS"][$this->GetValue($input["NAME"],0)])
					&& IsSet($input["ValidationErrorMessage"]))
						$input_error=$default_error;
				}
			}
		}
		return($input_error);
	}

	Function Validate(&$verify,$sub_form="")
	{
		for($inputs=array(), $input_number=0, Reset($this->inputs);$input_number<count($this->inputs);Next($this->inputs), $input_number++)
			$inputs[]=Key($this->inputs);
		for($invalid_parents=$custom=array(),$error="",$input_number=0;$input_number<count($inputs);$input_number++)
		{
			$field=$inputs[$input_number];
			if(strcmp($this->inputs[$field]["TYPE"],"custom"))
			{
				if(!IsSet($this->inputs[$field]["DiscardInvalidValues"]))
				{
					$input_error=$this->ValidateInput($field, $this->inputs[$field]["VALUE"], $sub_form);
					if(strlen($input_error))
					{
						if(IsSet($this->inputs[$field]["parent"]))
						{
							$parent=$this->inputs[$field]["parent"];
							$invalid_parents[$parent]=$field;
							$invalid_field=$parent;
						}
						else
							$invalid_field=$field;
						if(strlen($error)==0)
							$error=$input_error;
						$verify[$invalid_field]=$this->inputs[$invalid_field]["SubForm"];
					}
				}
			}
			else
				$custom[]=$field;
		}
		for($input_number=0;$input_number<count($custom);$input_number++)
		{
			$field=$custom[$input_number];
			if(!IsSet($invalid_parents[$field])
			&& !IsSet($this->inputs[$field]["DiscardInvalidValues"]))
			{
				$input_error=$this->inputs[$field]["object"]->ValidateInput($this);
				if(strlen($input_error))
				{
					if(strlen($error)==0)
						$error=$input_error;
						//JBA
					$errors[Key($this->inputs)]=$input_error;
					   //END JBA
					$verify[$field]=$this->inputs[$field]["SubForm"];
				}
			}
		}
		return($errors);  //JBA added "s" to variable
	}

	Function LoadInputValues($submitted=0)
	{
		for($radio=array(),$input_number=0,Reset($this->inputs);$input_number<count($this->inputs);Next($this->inputs),$input_number++)
		{
			$key=Key($this->inputs);
			if(!strcmp($this->inputs[$key]["TYPE"],"radio"))
			{
				$name=$this->inputs[$key]["NAME"];
				$value=(IsSet($this->inputs[$key]["VALUE"]) ? $this->inputs[$key]["VALUE"] : "on");
				if(IsSet($radio[$name]))
					$radio[$name][$value]=$key;
				else
					$radio[$name]=array($value=>$key);
			}
		}
		for($custom=$this->Changes=array(),$input_number=0,Reset($this->inputs);$input_number<count($this->inputs);Next($this->inputs),$input_number++)
		{
			$key=Key($this->inputs);
			if((IsSet($this->inputs[$key]["Accessible"]) ? !$this->inputs[$key]["Accessible"] : $this->ReadOnly))
				continue;
			switch($this->inputs[$key]["TYPE"])
			{
				case "submit":
				case "image":
				case "reset":
				case "button":
					break;
				case "radio":
					$name=$this->inputs[$key]["NAME"];
					if($this->IsSetValue($name,0))
					{
						$value=$this->GetValue($name,0);
						$radio_value=(IsSet($this->inputs[$key]["VALUE"]) ? $this->inputs[$key]["VALUE"] : "on");
						if(!strcmp(strtolower($value),strtolower($radio_value)))
						{
							if(!IsSet($this->inputs[$key]["CHECKED"]))
								$this->Changes[$key]="";
							$this->inputs[$key]["CHECKED"]=1;
						}
						else
						{
							if(IsSet($radio[$name][$value]))
							{
								if(IsSet($this->inputs[$key]["CHECKED"]))
									$this->Changes[$key]=$radio_value;
								Unset($this->inputs[$key]["CHECKED"]);
							}
						}
					}
					break;
				case "checkbox":
					$checkbox_value=(IsSet($this->inputs[$key]["VALUE"]) ? $this->inputs[$key]["VALUE"] : "on");
					if(IsSet($this->inputs[$key]["MULTIPLE"]))
					{
						$value=($this->IsSetValue($this->inputs[$key]["NAME"],0) ? $this->GetValue($this->inputs[$key]["NAME"],0,1) : array());
						if(GetType($value)=="array")
						{
							$checked=0;
							foreach($value as $item)
							{
								if(!strcmp($item,$checkbox_value))
								{
									$checked=1;
									break;
								}
							}
						}
					}
					else
						$checked=$this->IsSetValue($this->inputs[$key]["NAME"],0);
					if($checked)
					{
						if(!IsSet($this->inputs[$key]["CHECKED"]))
							$this->Changes[$key]="";
						$this->inputs[$key]["CHECKED"]=1;
					}
					else
					{
						if($submitted)
						{
							if(IsSet($this->inputs[$key]["CHECKED"]))
								$this->Changes[$key]=$checkbox_value;
							Unset($this->inputs[$key]["CHECKED"]);
						}
					}
					break;
				case "select":
					if(IsSet($this->inputs[$key]["MULTIPLE"]))
					{
						if($submitted)
						{
							$this->Changes[$key]=$this->inputs[$key]["SELECTED"];
							for($value_key=0,$this->Changes[$key];$value_key<count($this->Changes[$key]);Next($this->Changes[$key]),$value_key++)
								$this->Changes[$key][Key($this->Changes[$key])]=0;
							$value=($this->IsSetValue($key,0) ? $this->GetValue($key,0,1) : array());
							$this->inputs[$key]["SELECTED"]=array();
							if(GetType($value)=="array")
							{
								for($value_key=0,Reset($value);$value_key<count($value);Next($value),$value_key++)
								{
									$entry_value=$value[Key($value)];
									if(IsSet($this->inputs[$key]["OPTIONS"][$entry_value]))
									{
										if(IsSet($this->Changes[$key][$entry_value]))
											Unset($this->Changes[$key][$entry_value]);
										else
											$this->Changes[$key][$entry_value]=1;
										$this->inputs[$key]["SELECTED"][$entry_value]=1;
									}
								}
							}
							if(count($this->Changes[$key])==0)
								Unset($this->Changes[$key]);
						}
					}
					else
					{
						$previous_value=$this->inputs[$key]["VALUE"];
						if($this->IsSetValue($key,0))
						{
							$value=$this->GetValue($key,0);
							if(IsSet($this->inputs[$key]["OPTIONS"][$value]))
								$this->inputs[$key]["VALUE"]=$value;
						}
						if(strcmp($this->inputs[$key]["VALUE"],$previous_value))
							$this->Changes[$key]=$previous_value;
					}
					break;
				case "custom":
					$custom[]=$key;
					break;
				default:
					if($this->IsSetValue($key,$this->inputs[$key]["TYPE"]=="file"))
					{
						$value=$this->GetValue($key,$this->inputs[$key]["TYPE"]=="file");
						switch(GetType($value))
						{
							case "string":
							case "integer":
							case "double":
								$set_value=1;
								switch($this->inputs[$key]["TYPE"])
								{
									case "text":
									case "file":
									case "password":
										if(IsSet($this->inputs[$key]["MAXLENGTH"]))
										{

											$max_length=$this->inputs[$key]["MAXLENGTH"];
											if(strlen($value)>$max_length)
											{
												$value=substr($value,0,$max_length);
												$set_value=1;
											}
										}
										break;
								}
								if(IsSet($this->inputs[$key]["Capitalization"]))
								{
									switch($this->inputs[$key]["Capitalization"])
									{
										case "uppercase":
											$function=$this->toupper_function;
											$value=$function($value);
											$set_value=1;
											break;
										case "lowercase":
											$function=$this->tolower_function;
											$value=$function($value);
											$set_value=1;
											break;
										case "words":
											$lower_function=$this->tolower_function;
											$upper_function=$this->toupper_function;
											for($word=1,$position=0;$position<strlen($value);$position++)
											{
												switch($character=$value[$position])
												{
													case " ":
													case "\t":
													case "\n":
													case "\r":
														$word=1;
														break;
													default:
														$value[$position]=($word ? $upper_function($character) : $lower_function($character));
														$word=0;
														break;
												}
											}
											$set_value=1;
											break;
									}
								}
								if(IsSet($this->inputs[$key]["ReplacePatterns"])
								&& count($this->inputs[$key]["ReplacePatterns"]))
								{
									for($pattern=0,Reset($this->inputs[$key]["ReplacePatterns"]);$pattern<count($this->inputs[$key]["ReplacePatterns"]);Next($this->inputs[$key]["ReplacePatterns"]),$pattern++)
									{
										$expression=Key($this->inputs[$key]["ReplacePatterns"]);
										$value=ereg_replace($expression,$this->inputs[$key]["ReplacePatterns"][$expression],$value);
										$set_value=1;
									}
								}
								if(IsSet($this->inputs[$key]["DiscardInvalidValues"]))
								{
									$input_error=$this->ValidateInput($key,$value);
									if(strlen($input_error))
									{
										if(IsSet($this->inputs[$key]["VALUE"]))
											$this->SetValue($key,$this->inputs[$key]["VALUE"]);
										break;
									}
								}
								$previous_value=(IsSet($this->inputs[$key]["VALUE"]) ? $this->inputs[$key]["VALUE"] : "");
								if(strcmp($value,$previous_value))
									$this->Changes[$key]=$previous_value;
								$this->inputs[$key]["VALUE"]=$value;
								if($set_value)
									$this->SetValue($key,$value);
								break;
						}
					}
			}
		}
		for($input_number=0,Reset($custom);$input_number<count($custom);Next($custom),$input_number++)
			$this->inputs[$custom[$input_number]]["object"]->LoadInputValues($this, $submitted);
	}

	Function SetInputProperty($input,$property,$value)
	{
		if(!IsSet($this->inputs[$input]))
			return($this->OutputError("it was not specified a valid input (3): $input, $property, $value",$input));
		if(!strcmp($this->inputs[$input]["TYPE"],"custom"))
		{
			if(strlen($error=$this->inputs[$input]["object"]->SetInputProperty($this, $property, $value)))
				$this->OutputError($error,$input);
			return("");
		}
		switch($property)
		{
			case "ACCEPT":
			case "ALT":
			case "BORDER":
			case "COLS":
			case "MAXLENGTH":
			case "ONCHANGE":
			case "ONCLICK":
			case "ROWS":
			case "SIZE":
			case "SRC":
			case "TABINDEX":
			case "Accessible":
			case "Capitalization":
			case "ClientScript":
			case "SubForm":
			case "VALUE":
			case "STYLE":
			case "CLASS":
				break;
			default:
				return($this->OutputError("it was not specified a valid settable input property",$property));
		}
		$this->inputs[$input][$property]=$value;
		return("");
	}

	Function SetInputValue($input,$value)
	{
		return($this->SetInputProperty($input,"VALUE",$value));
	}

	Function GetInputValue($input)
	{
		//if(!IsSet($this->inputs[$input]))
		//	return($this->OutputError("it was not specified a valid input",$input));
		// JBA - this will give an odd error message if $input is not valid
		if(!IsSet($this->inputs[$input])) {
			return($this->GetCheckedRadioValue($input));
		}
		// end JBA
		switch($this->inputs[$input]["TYPE"])
		{
			case "custom":
				return($this->inputs[$input]["object"]->GetInputValue($this));
			case "select":
				if(IsSet($this->inputs[$input]["MULTIPLE"]))
				{
					$value=array();
					for(Reset($this->inputs[$input]["SELECTED"]),$selected=0;$selected<count($this->inputs[$input]["SELECTED"]);Next($this->inputs[$input]["SELECTED"]),$selected++)
						$value[]=Key($this->inputs[$input]["SELECTED"]);
					return($value);
				}
			default:
				return(IsSet($this->inputs[$input]["VALUE"]) ? $this->inputs[$input]["VALUE"] : "");
		}
	}

	Function GetJavascriptInputObject($form_object, $input)
	{
		return($form_object."[".$this->EncodeJavascriptString($input)."]");
	}

	Function GetJavascriptInputValue($form_object, $input)
	{
		switch($this->inputs[$input]["TYPE"])
		{
			case "select":
				if(strlen($field=$this->GetJavascriptInputObject($form_object, $input))==0)
					return("");
				return($field.".options[".$field.".selectedIndex].value");
			case "custom":
				if(strlen($javascript=$this->inputs[$input]["object"]->GetJavascriptInputValue($this, $form_object)))
					return($javascript);
				$this->OutputError("could not retrieve the Javascript input value", $input);
				return("");
			default:
				if(strlen($field=$this->GetJavascriptInputObject($form_object, $input))==0)
					return("");
				return($field.".value");
		}
	}

	Function SetCheckedState($input,$checked)
	{
		if(!IsSet($this->inputs[$input])
		|| ($this->inputs[$input]["TYPE"]!="radio"
		&& $this->inputs[$input]["TYPE"]!="checkbox"))
			return($this->OutputError("it was not specified a valid radio or checkbox input",$input));
		if($checked)
			$this->inputs[$input]["CHECKED"]=1;
		else
			Unset($this->inputs[$input]["CHECKED"]);
		return("");
	}

	Function GetCheckedState($input)
	{
		if(!IsSet($this->inputs[$input])
		|| ($this->inputs[$input]["TYPE"]!="radio"
		&& $this->inputs[$input]["TYPE"]!="checkbox"))
			return($this->OutputError("it was not specified a valid radio or checkbox input",$input));
		return(IsSet($this->inputs[$input]["CHECKED"]));
	}

	Function GetCheckedRadio($name)
	{
		for(Reset($this->inputs);GetType($input=Key($this->inputs))=="string";Next($this->inputs))
		{
			if(!strcmp($this->inputs[$input]["NAME"],$name))
			{
				if($this->inputs[$input]["TYPE"]!="radio")
				{
					$this->OutputError("the input of NAME \"$name\" is not a valid radio input");
					return("");
				}
				if(IsSet($this->inputs[$input]["CHECKED"]))
					return($input);
			}
		}
		return("");
	}

	Function GetCheckedRadioValue($name,$default="")
	{
		return(strlen($input=$this->GetCheckedRadio($name)) ? $this->inputs[$input]["VALUE"] : $default);
	}

	Function ResetFormParts()
	{
		for($input=0,Reset($this->inputs);$input<count($this->inputs);Next($this->inputs),$input++)
		{
			$input_name=Key($this->inputs);
			Unset($this->inputs[$input_name]["InputElement"]);
			Unset($this->inputs[$input_name]["Part"]);
		}
		$this->parts=$this->types=$this->input_parts=$this->input_elements=$this->functions=$this->label_access_keys=array();
		$this->client_validate=0;
		$this->hidden_parts=$this->accessible_parts=0;
	}

	Function AddHiddenInputs($inputs)
	{
		for($input=0,Reset($inputs);$input<count($inputs);Next($inputs),$input++)
		{
			$name=Key($inputs);
			if(strcmp($error=$this->AddInput(array(
				"TYPE"=>"hidden",
				"NAME"=>$name,
				"VALUE"=>$inputs[$name]
			)),""))
				return($error);
		}
		return("");
	}

	Function AddHiddenInputsParts($inputs)
	{
		for($input=0,Reset($inputs);$input<count($inputs);Next($inputs),$input++)
		{
			$name=Key($inputs);
			if(strcmp($error=$this->SetInputValue($name,$inputs[$name]),"")
			|| strcmp($error=$this->AddInputHiddenPart($name),""))
				return($error);
		}
		return("");
	}

	Function WasSubmitted($input="")
	{
		if(strcmp($input,""))
		{
			if(!IsSet($this->inputs[$input]))
			{
				$this->OutputError("it was not specified an existing input",$input);
				return("");
			}
			$name=(IsSet($this->inputs[$input]["NAME"]) ? $this->inputs[$input]["NAME"] : $input);
			if($this->inputs[$input]["TYPE"]=="image")
				return(($this->IsSetValue($name."_x",0) && $this->IsSetValue($name."_y",0)) ? $input : "");
			return($this->IsSetValue($name,$this->inputs[$input]["TYPE"]=="file") ? $input : "");
		}
		for($field=0,Reset($this->inputs);$field<count($this->inputs);Next($this->inputs),$field++)
		{
			$input=Key($this->inputs);
			$name=(IsSet($this->inputs[$input]["NAME"]) ? $this->inputs[$input]["NAME"] : $input);
			switch($this->inputs[$input]["TYPE"])
			{
				case "submit":
					if($this->IsSetValue($name,0))
						return($input);
					break;
				case "image":
					if($this->IsSetValue($name."_x",0)
					&& $this->IsSetValue($name."_y",0))
						return($input);
					break;
			}
		}
		return("");
	}

	Function StartLayoutCapture()
	{
		if($this->capturing)
			return($this->OutputError("the form layout is already being captured","StartLayoutCapture"));
		if(!$this->capturing=ob_start())
			return($this->OutputError("could not start capturing the form layout","StartLayoutCapture"));
		return("");
	}

	Function EndLayoutCapture()
	{
		if(!$this->capturing)
			return($this->OutputError("the form layout was not being captured","EndLayoutCapture"));
		$data=ob_get_contents();
		ob_end_clean();
		$this->capturing=0;
		$this->AddDataPart($data);
		return("");
	}

	Function FetchOutput()
	{
		global $form_output;

		$form_output="";
		$arguments=array(
			"Function"=>"FormAppendOutput",
			"EndOfLine"=>$this->end_of_line
		);
		return(strlen($this->OutputError($this->Output($arguments))) ? "" : $form_output);
	}

	Function DisplayOutput()
	{
		$arguments=array(
			"Function"=>"FormDisplayOutput",
			"EndOfLine"=>$this->end_of_line
		);
		return($this->Output($arguments));
	}
};

Function FormCaptureOutput(&$form,$arguments)
{
	global $form_output;

	$form_output="";
	$arguments["Function"]="FormAppendOutput";
	return($form->OutputError($form->Output($arguments)) ? "" : $form_output);
}

}
?>