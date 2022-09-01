<?php

$boa = array
(
  "type" => "odbc_mssql",
  'dsn'  => "odbc_mssql://aoi_app:bobjects@SQLBO-DSN",
  "host" => "bowsql2", 
  "name" => "dwsql",
  "user" => "aoi_app",
  "pass" => "bobjects"
);

require_once 'classes/adodb/adodb.inc.php';

$db_type = $boa["type"];
$db_dsn = $boa["dsn"];
$db_host = $boa["host"];
$db_user = $boa["user"];
$db_pass = $boa["pass"];
$db_name = $boa["name"];

$db = ADONewConnection($db_dsn);

$db->debug = TRUE;

// $status = $db->Connect($db_host, $db_user, $db_pass);

$db->setFetchMode(ADODB_FETCH_NUM);

$db->SetFetchMode(ADODB_FETCH_ASSOC);

$query = "SELECT DISTINCT dwchannelcode, dwchanneldesc ".
         "FROM regionxlate ".
         "WHERE dwchannelcode > 0 ".
         "ORDER BY dwchannelcode";
$channels = $db->GetAll($query);

echo '<pre>'; print_r($channels); echo '</pre>';

exit;

?>