<?php
/*
 *
 * @(#) $Id: form_date.php,v 1.5 2004/07/14 07:29:12 mlemos Exp $
 *
 */

class form_date_class extends form_custom_class
{
	var $format="{year}-{month}-{day}";
	var $validation_start_date="";
	var $validation_start_date_error_message="";
	var $validation_end_date="";
	var $validation_end_date_error_message="";
	var $invalid_date_error_message="It was not specified a valid date.";
	var $invalid_year_error_message="It was not specified a valid year.";
	var $invalid_month_error_message="It was not specified a valid month";
	var $invalid_day_error_message="It was not specified a valid day.";

	var $year="";
	var $month="";
	var $day="";

	Function ValidateDateValue($year, $month, $day)
	{
		if(intval($year)<=0)
			return($this->invalid_year_error_message);
		switch($month)
		{
			case "01":
			case "03":
			case "05":
			case "07":
			case "08":
			case "10":
			case "12":
				$month_days=31;
				break;
			case "02":
				$is_leap_year=(($year % 4)==0 && (($year % 100)!=0 || ($year % 400)==0));
				$month_days=($is_leap_year ? 29 : 28);
				break;
			case "04":
			case "06":
			case "09":
			case "11":
				$month_days=30;
				break;
			default:
				return($this->invalid_month_error_message);
		}
		if($day>$month_days)
			return($this->invalid_day_error_message);
		return("");
	}

	Function ValidateDate($date, &$year, &$month, &$day)
	{
		if(!ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2})\$",$date,$matches))
			return($this->invalid_date_error_message);
		$year=$matches[1];
		$month=$matches[2];
		$day=$matches[3];
		return($this->ValidateDateValue($year, $month, $day));
	}

	Function AddInput(&$form, $arguments)
	{
		$this->year=$this->GenerateInputID($form, $this->input, "year");
		$this->month=$this->GenerateInputID($form, $this->input, "month");
		$this->day=$this->GenerateInputID($form, $this->input, "day");
		$this->valid_marks=array(
			"input"=>array(
				"year"=>$this->year,
				"month"=>$this->month,
				"day"=>$this->day
			)
		);
		$format=(IsSet($arguments["Format"]) ? $arguments["Format"] : $this->format);
		if(strlen($error=$this->DefaultSetInputProperty(&$form, "Format", $format)))
			return($error);
		$validation_error_message=(IsSet($arguments["ValidationErrorMessage"]) ? $arguments["ValidationErrorMessage"] : "");
		if(IsSet($arguments["ValidationStartDate"]))
		{
			if(strlen($error=$this->ValidateDate($arguments["ValidationStartDate"], $current_year, $current_month, $current_day)))
				return($error);
			$this->validation_start_date_error_message=(IsSet($arguments["ValidationStartDateErrorMessage"]) ? $arguments["ValidationStartDateErrorMessage"] : $validation_error_message);
			if(strlen($this->validation_start_date_error_message)==0)
				return("it was not specified a valid start date validation error message");
			$this->validation_start_date=$arguments["ValidationStartDate"];
		}
		if(IsSet($arguments["ValidationEndDate"]))
		{
			if(strlen($error=$this->ValidateDate($arguments["ValidationEndDate"], $current_year, $current_month, $current_day)))
				return($error);
			$this->validation_end_date_error_message=(IsSet($arguments["ValidationEndDateErrorMessage"]) ? $arguments["ValidationEndDateErrorMessage"] : $validation_error_message);
			if(strlen($this->validation_end_date_error_message)==0)
				return("it was not specified a valid end date validation error message");
			$this->validation_end_date=$arguments["ValidationEndDate"];
		}
		if(IsSet($arguments["VALUE"])
		&& strlen($arguments["VALUE"]))
		{
			if(strlen($error=$this->ValidateDate($arguments["VALUE"], $current_year, $current_month, $current_day)))
				return($error);
		}
		else
			$current_year=$current_month=$current_day="";
		$month_options=array(""=>"");
		if(IsSet($arguments["Months"]))
		{
			for($month=1; $month<=12; $month++)
			{
				$month_value=sprintf("%02d", $month);
				if(!IsSet($arguments["Months"][$month_value]))
					return("it was not specified the value for month ".$month_value);
				$month_options[$month_value]=$arguments["Months"][$month_value];
			}
		}
		else
		{
			for($month=1; $month<=12; $month++)
			{
				$month_value=sprintf("%02d", $month);
				$month_options[$month_value]=$month_value;
			}
		}
		$day_options=array(""=>"");
		for($day=1; $day<=31; $day++)
			$day_options[sprintf("%02d",$day)]=sprintf("%2d",$day);
		if(strlen($error=$form->AddInput(array(
			"NAME"=>$this->year,
			"ID"=>$this->year,
			"TYPE"=>"text",
			"MAXLENGTH"=>4,
			"SIZE"=>5,
			"VALUE"=>$current_year,
			"ValidateAsInteger"=>1,
			"ValidationLowerLimit"=>1,
			"ValidationErrorMessage"=>$this->invalid_year_error_message
		)))
		|| strlen($error=$form->AddInput(array(
			"NAME"=>$this->month,
			"ID"=>$this->month,
			"TYPE"=>"select",
			"OPTIONS"=>$month_options,
			"VALUE"=>$current_month,
			"ValidateAsNotEmpty"=>1,
			"ValidationErrorMessage"=>$this->invalid_month_error_message
		)))
		|| strlen($error=$form->AddInput(array(
			"NAME"=>$this->day,
			"ID"=>$this->day,
			"TYPE"=>"select",
			"OPTIONS"=>$day_options,
			"VALUE"=>$current_day,
			"ValidateAsNotEmpty"=>1,
			"ValidationErrorMessage"=>$this->invalid_day_error_message
		))))
			return($error);
		return("");
	}

	Function ValidateInput(&$form)
	{
		$year=$form->GetInputValue($this->year);
		$month=$form->GetInputValue($this->month);
		$day=$form->GetInputValue($this->day);
		if(strlen($error=$this->ValidateDateValue($year, $month, $day)))
			return($error);
		$date=sprintf("%04d-%02d-%02d", $year, $month, $day);
		if(strlen($this->validation_start_date)
		&& strcmp($date,$this->validation_start_date)<0)
			return($this->validation_start_date_error_message);
		if(strlen($this->validation_end_date)
		&& strcmp($date,$this->validation_end_date)>0)
			return($this->validation_end_date_error_message);
		return("");
	}

	Function SetInputProperty(&$form, $property, $value)
	{
		switch($property)
		{
			case "VALUE":
				if(strlen($error=$this->ValidateDate($value, &$year, &$month, &$day))
				|| strlen($error=$form->SetInputProperty($this->year, "VALUE", $year))
				|| strlen($error=$form->SetInputProperty($this->month, "VALUE", $month))
				|| strlen($error=$form->SetInputProperty($this->day, "VALUE", $day)))
					return($error);
				break;
			default:
				return($this->DefaultSetInputProperty($form, $property, $value));
		}
		return("");
	}

	Function GetInputValue(&$form)
	{
		if(strlen($year=$form->GetInputValue($this->year))==0
		|| strlen($month=$form->GetInputValue($this->month))==0
		|| strlen($day=$form->GetInputValue($this->day))==0)
			return("");
		return(sprintf("%04d-%02d-%02d", $year, $month, $day));
	}

	Function GetJavascriptValidations(&$form, $form_object, &$validations)
	{
		if(strlen($day=$form->GetJavascriptInputValue($form_object,$this->day))==0)
			return("it was not possible to retrieve the day input Javascript value");
		if(strlen($month=$form->GetJavascriptInputValue($form_object,$this->month))==0)
			return("it was not possible to retrieve the day input Javascript value");
		if(strlen($year=$form->GetJavascriptInputValue($form_object,$this->year))==0)
			return("it was not possible to retrieve the day input Javascript value");
		$validations=array(
			array(
				"Commands"=>array(
					"month=".$month,
					"if(month=='04'",
					"|| month=='06'",
					"|| month=='09'",
					"|| month=='11')",
					"\tmonth_days=30",
					"else",
					"{",
					"\tif(month=='02')",
					"\t{",
					"\t\tyear=parseInt(".$year.")",
					"\t\tif((year % 4)==0",
					"\t\t&& ((year % 100)!=0",
					"\t\t|| (year % 400)==0))",
					"\t\t\tmonth_days=29",
					"\t\telse",
					"\t\t\tmonth_days=28",
					"\t}",
					"\telse",
					"\t\tmonth_days=31",
					"}",
					"date_day=".$day
				),
				"Condition"=>"month_days<parseInt(date_day)",
				"ErrorMessage"=>$this->invalid_day_error_message,
				"Focus"=>$this->day
			)
		);
		if(strlen($this->validation_start_date)
		|| strlen($this->validation_end_date))
		{
			$commands=array(
				"date=(".$year.".length<3 ? '00' : '') + ((".$year.".length % 2) ? '0' : '') + ".$year." + '-' + month + '-' + date_day"
			);
		}
		else
			$commands=array();
		if(strlen($this->validation_start_date))
		{
			$validations[]=array(
				"Commands"=>$commands,
				"Condition"=>"date<".$form->EncodeJavascriptString($this->validation_start_date),
				"ErrorMessage"=>$this->validation_start_date_error_message
			);
			$commands=array();
		}
		if(strlen($this->validation_end_date))
		{
			$validations[]=array(
				"Commands"=>$commands,
				"Condition"=>$form->EncodeJavascriptString($this->validation_end_date)."<date",
				"ErrorMessage"=>$this->validation_end_date_error_message
			);
			$commands=array();
		}
		return("");
	}

	Function GetJavascriptInputValue($form, $form_object)
	{
		if(strlen($day=$form->GetJavascriptInputValue($form_object,$this->day))==0
		|| strlen($month=$form->GetJavascriptInputValue($form_object,$this->month))==0
		|| strlen($year=$form->GetJavascriptInputValue($form_object,$this->year))==0)
			return("");
		return("((".$year.".length<3 ? '00' : '') + ((".$year.".length % 2) ? '0' : '') + ".$year." + '-' + ".$month." + '-' + ".$day.")");
	}
};

?>