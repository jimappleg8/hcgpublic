<?php

//---------------------------------------------------------------------------
// form definition : pr_field
//---------------------------------------------------------------------------

   $FORM_SAVE_ARRAY = array(
      "FieldID"        => "number",
      "FieldName"
      "SaveType"
      "InSaveArray"
      "CreateType"
      "InDataArray"    = "number",
      "DisplayInForm"  => "number",
      "TYPE"
      "NAME"
      "ID"
      "VALUE"
      "ACCEPT"
      "ACCESSKEY"
      "ALT"
      "BORDER"
      "CHECKED"
      "CLASS"
      "COLS"
      "LABEL"
      "MAXLENGTH"
      "MULTIPLE"
      "ONCHANGE"
      "ONCLICK"
      "OPTIONS"
      "ROWS"
      "SELECTED"
      "SIZE"
      "STYLE"
      "TABINDEX"
      "Accessible"
      "Capitalization"
      "ClientScript"
      "Encoding"
      "EncoderField"
      "EncodingFunctionVerification"
      "EncodingFunctionScriptFile"
      "ExtraAttributes"
      "ReadOnlyMark"
      "ReplaceExpressions"
      "SubForm"
      "ValidateErrorMessage" => "text",
      "ValidateAsEmail"
      "ValidateAsEmailErrorMessage" => "text",
      "ValidateAsCreditCard"
      "ValidateAsCreditCardTypeField"
      "ValidateAsCreditCardErrorMessage" => "text",
      "ValidateRegularExpression"
      "ValidateRegularExpressionErrorMessage" => "text",
      "ValidateAsNotEmpty"
      "ValidateAsNotEmptyErrorMessage" => "text",
      "ValidateMinimumLength"
      "ValidateMinimumLengthErrorMessage" => "text",
      "ValidateAsEqualTo"
      "ValidateAsEqualToErrorMessage" => "text",
      "ValidateAsDifferentFrom"
      "ValidateAsDifferentFromErrorMessage" => "text",
      "ValidateAsSet"
      "ValidateAsSetErrorMessage" => "text",
      "ValidateAsInteger"
      "ValidateAsIntegerErrorMessage" => "text",
      "ValidateAsFloat"
      "ValidateAsFloatErrorMessage" => "text",
      "ValidateOnlyOnClientSide" => "number",
      "ValidateOnlyOnServerSide" => "number",
      "ValidateOptionalValue" => "text",
      "ValidationLowerLimit"
      "ValidationUpperLimit"
      "ValidationDecimalPlaces"
      "ValidationClientFunction" => "text",
      "ValidationClientFunctionErrorMessage" => "text",
      "ValidationServerFunction" => "text",
      "ValidationServerFunctionErrorMessage" => "text",
   );
   
   
   $TABLE_CREATE_STATEMENT = "create table pr_product (".
      "FieldID int(11) unsigned not null auto_increment primary key, ".
      "FieldName".
      "SaveType".
      "InSaveArray".
      "CreateType".
      "InDataArray int(11)".
      "DisplayInForm int(11)".
      "TYPE".
      "NAME".
      "ID".
      "VALUE".
      "ACCEPT".
      "ACCESSKEY".
      "ALT".
      "BORDER".
      "CHECKED".
      "CLASS".
      "COLS".
      "LABEL".
      "MAXLENGTH".
      "MULTIPLE".
      "ONCHANGE".
      "ONCLICK".
      "OPTIONS".
      "ROWS".
      "SELECTED".
      "SIZE".
      "STYLE".
      "TABINDEX".
      "Accessible".
      "Capitalization".
      "ClientScript".
      "Encoding".
      "EncoderField".
      "EncodingFunctionVerification".
      "EncodingFunctionScriptFile".
      "ExtraAttributes".
      "ReadOnlyMark".
      "ReplaceExpressions".
      "SubForm".
      "ValidateErrorMessage char(255), ".
      "ValidateAsEmail"
      "ValidateAsEmailErrorMessage char(255), ".
      "ValidateAsCreditCard"
      "ValidateAsCreditCardTypeField"
      "ValidateAsCreditCardErrorMessage char(255), ".
      "ValidateRegularExpression"
      "ValidateRegularExpressionErrorMessage char(255), ".
      "ValidateAsNotEmpty"
      "ValidateAsNotEmptyErrorMessage char(255), ".
      "ValidateMinimumLength"
      "ValidateMinimumLengthErrorMessage char(255), ".
      "ValidateAsEqualTo"
      "ValidateAsEqualToErrorMessage char(255), ".
      "ValidateAsDifferentFrom"
      "ValidateAsDifferentFromErrorMessage char(255), ".
      "ValidateAsSet"
      "ValidateAsSetErrorMessage char(255), ".
      "ValidateAsInteger"
      "ValidateAsIntegerErrorMessage char(255), ".
      "ValidateAsFloat"
      "ValidateAsFloatErrorMessage char(255), ".
      "ValidateOnlyOnClientSide" => "number",
      "ValidateOnlyOnServerSide" => "number",
      "ValidateOptionalValue char(255), ".
      "ValidationLowerLimit"
      "ValidationUpperLimit"
      "ValidationDecimalPlaces"
      "ValidationClientFunction char(128), ".
      "ValidationClientFunctionErrorMessage char(255), ".
      "ValidationServerFunction char(128), ".
      "ValidationServerFunctionErrorMessage char(255)".
   ")";

   
?>