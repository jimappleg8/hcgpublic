<?php
//*****************************************************************************
// This contains settings that in my main application are obtained from the
// the MENU database. As this sample aplication has no such database the
// settings must be done manually
//*****************************************************************************

// define the top-level buttons in the menu bar
$_TABLE['menu'] = array();
$_TABLE['menu'][1] = array( 'TableID'        => 'site',
                            'ButtonText'     => 'Edit Sites', 
                            'DeffaultTaskID' => 'list' );
$_TABLE['menu']['site_type'] = array('button_text' => 'Site Type', 'task_id' => 'site_type_list.php');
$_TABLE['menu']['option']    = array('button_text' => 'Option', 'task_id' => 'option_list.php');
$_TABLE['menu']['tree_type'] = array('button_text' => 'Tree Type', 'task_id' => 'tree_type_list.php');

if (!isset($_TABLE['table_id'])) {
    $_TABLE['table_id'] = $outer_table;
} // if
// set one of these buttons as 'active'
switch ($_TABLE['table_id']) {
	case 'x_option': 
		$_TABLE['menu_buttons']['option']['active'] = 'y';
		break;
	case 'site': 
		$_TABLE['menu_buttons']['site']['active'] = 'y';
		break;
    case 'site_type': 
		$_TABLE['menu_buttons']['site_type']['active'] = 'y';
		break;
	case 'tree_type': 
		$_TABLE['menu_buttons']['tree_type']['active'] = 'y';
		break;
    default:
		;
} // switch

// in the following array there is one entry for each script ....
// $title       = the form title
// $button_text = the breadcrumb area of the navigation bar
// $nav_buttons = array of optional navigation buttons
switch (basename($_SERVER['PHP_SELF'])) {
    case 'site.php':
        $_TABLE['title'] = 'Edit Sites';
        $_TABLE['button_text'] = 'Edit Site';
        $_TABLE['nav_buttons'][] = array('button_text' => 'New', 'task_id' => 'site_add.php', 'context_preselect' => 'N');
        $_TABLE['nav_buttons'][] = array('button_text' => 'Update', 'task_id' => 'site_upd.php', 'context_preselect' => 'Y');
        $_TABLE['nav_buttons'][] = array('button_text' => 'Read', 'task_id' => 'site_enq.php', 'context_preselect' => 'Y');
        $_TABLE['nav_buttons'][] = array('button_text' => 'Delete', 'task_id' => 'site_del.php', 'context_preselect' => 'Y');
        $_TABLE['nav_buttons'][] = array('button_text' => 'Search', 'task_id' => 'site_search.php', 'context_preselect' => 'N');
		break;
    default:
		$_TABLE['title'] = 'unknown';
        $_TABLE['button_text'] = 'unknown';
} // switch

?>