<?php
$_TABLE['structure']['xsl_file'] = 'std.list1.xsl';

$_TABLE['structure']['tables']['main'] = 'site';

$_TABLE['structure']['main']['columns'][] = array('width' => 60);
$_TABLE['structure']['main']['columns'][] = array('width' => 40);
$_TABLE['structure']['main']['columns'][] = array('width' => 200);
$_TABLE['structure']['main']['columns'][] = array('width' => '*');

$_TABLE['structure']['main']['fields'] = array('selectbox' => 'Select',
                                               'SiteID' => 'ID',
                                               'BrandName' => 'Brand Name',
                                               'BaseURL' => 'Base URL');
?>