<?php
/**
 * Smarty prefilter: global formsess prefilter - experimental
 * Transforms all the <FS:.../> tags to {fs} smarty functions
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: prefilter.fs.php,v 1.4 2003/05/19 15:50:09 katana Exp $
 */
function smarty_prefilter_fs($source, &$smarty) {
  $filter = new fs_filter($source, $smarty);
  return $filter->getSource();
}
?>