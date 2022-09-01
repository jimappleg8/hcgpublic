#!/usr/local/bin/php

<?php

//require_once 'config.inc.php';
$_HCG_GLOBAL['application_dir'] = "/var/opt/httpd";
$_HCG_GLOBAL['lib_dir'] = $_HCG_GLOBAL['application_dir']."/lib";
$_HCG_GLOBAL['classes_dir'] = $_HCG_GLOBAL['lib_dir']."/classes";
$_HCG_GLOBAL['hcg_classes_dir'] = $_HCG_GLOBAL['classes_dir']."/hcg_public";
$_HCG_GLOBAL['pear_dir'] = $_HCG_GLOBAL['classes_dir']."/pear";

$hcg_include_path = $_HCG_GLOBAL['pear_dir']. ":" .
                    $_HCG_GLOBAL['hcg_classes_dir']. ":".
                    ini_get('include_path');

ini_set("include_path",$hcg_include_path);

require_once 'ir.inc.php';

downloadXmlFeeds();

?>