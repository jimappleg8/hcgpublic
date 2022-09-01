<?php

// =========================================================================
// menu.fns.php
// written by Jim Applegate
//
// =========================================================================


require_once("template.class.php");


// ------------------------------------------------------------------------
// TAG: cascading_menu
//   Creates a cascading menu from the menu database. It depends on the
//   menu database being set up correctly with parents defined before
//   children. In an SQL database, the returned data will probably need
//   to be sorted.
//
// ------------------------------------------------------------------------

function cascading_menu($top, $left, $height, $width, $css = "menuItem")
{
   require_once('cascading_menu.class');

   $menu = _getMenuDataFlat();

   // the $root variable is so named because of the parent "root".
   $root = new cascading_menu($top, $left, $height, $width, $css);

   // first we must create all the menu objects

   $max_level = 0;
   foreach ($menu as $key => $row) {

      if ($row['parent'] != "") {

         // sets a level attribute to point to correct CSS style
         if ($frame[$row['parent']]['level'] >= 0) {
            $frame[$row['sec_id']]['level'] = $frame[$row['parent']]['level'] + 1;
            $css_level = $frame[$row['sec_id']]['level'];
            if ($css_level > $max_level) {
               $max_level = $css_level;
            }
         } else {
            echo "ERROR: (cascading_menus) Parent (".$row['parent'].") not defined.<br>";
         }

         // Creates the menu object for this item. Assumes a specific
         //   location for the images and assumes they are GIFs.
         $$row['sec_id'] = new choice("/images/menu/".$row['sec_id']."_up.gif", "/images/menu/".$row['sec_id']."_dn.gif", $row['link'], $row['sec_name'], "menuItem$css_level");
         $frame[$row['sec_id']]['parent'] = $row['parent'];
         $frame[$row['sec_id']]['name'] = $row['sec_id'];

      } else {
         $frame[$row['sec_id']]['level'] = 0;  // this is the root level
      }
   }

   // then we must build the menu in order
   for ($i = 1; $i < $max_level+1; ++$i) {
      foreach ($frame as $key => $row) {
         if ($row['level'] == $i) {
            $$row['parent'] -> add($$row['name']);
         }
      }
   }
   echo $root->write();
}


// ------------------------------------------------------------------------
// TAG: slideout_menu
//   Creates a slideout menu from the menu database. It uses the JavaScript
//   available on YoungPup.net
//
// ------------------------------------------------------------------------

function slideout_menu($top, $left, $height, $width, $css = "menuItem")
{

   $menu = _getMenuDataFlat();

   // the $root variable is so named because of the parent "root".
   $root = new cascading_menu($top, $left, $height, $width, $css);

   // first we must create all the menu objects

   $max_level = 0;
   foreach ($menu as $key => $row) {

      if ($row['parent'] != "") {

         // sets a level attribute to point to correct CSS style
         if ($frame[$row['parent']]['level'] >= 0) {
            $frame[$row['sec_id']]['level'] = $frame[$row['parent']]['level'] + 1;
            $css_level = $frame[$row['sec_id']]['level'];
            if ($css_level > $max_level) {
               $max_level = $css_level;
            }
         } else {
            echo "ERROR: (cascading_menus) Parent (".$row['parent'].") not defined.<br>";
         }

         // Creates the menu object for this item. Assumes a specific
         //   location for the images and assumes they are GIFs.
         $$row['sec_id'] = new choice("/images/menu/".$row['sec_id']."_up.gif", "/images/menu/".$row['sec_id']."_dn.gif", $row['link'], $row['sec_name'], "menuItem$css_level");
         $frame[$row['sec_id']]['parent'] = $row['parent'];
         $frame[$row['sec_id']]['name'] = $row['sec_id'];

      } else {
         $frame[$row['sec_id']]['level'] = 0;  // this is the root level
      }
   }

   // then we must build the menu in order
   for ($i = 1; $i < $max_level+1; ++$i) {
      foreach ($frame as $key => $row) {
         if ($row['level'] == $i) {
            $$row['parent'] -> add($$row['name']);
         }
      }
   }
   echo $root->write();
}


// ------------------------------------------------------------------------
// TAG: left_menu
//
// ------------------------------------------------------------------------

function left_menu($item_id, $root_level = 2, $hilitepath = false, $tpl = 'left_menu.tpl')
{
   $menu = _getMenuDataFlat();

   // we start with level 1 as the level of the actual item, then we
   // reverse levels later...
   $level = 1;
   $item = $item_id;
   $direct_line = array();
   $next_item = '';
   while ($item != "") {
      foreach ($menu as $key => $row) {
         // mark all the sub items of the requested menu item. Each time
         // through the loop, this gets all the children, peers, and
         // parents in this branch of the tree, up to the root level.
         if ($menu[$key]['parent'] == $item) {
            $menu[$key]['level'] = $level;
            $menu[$key]['use'] = true;
         }
         // somewhere in this process we'll find the desired menu item.
         // when we do, we make a note of it's position in the array.
         if ($menu[$key]['sec_id'] == $item_id) {
            $item_key = $key;
            $menu[$key]['hilite'] = true;
         }
         // get the parent for next time through 'while' loop
         if ($menu[$key]['sec_id'] == $item) {
            $direct_line[] = $key;
            $next_item = $menu[$key]['parent'];
         }
      }
      $item = $next_item;
      $level = $level + 1;
   }

   $count = 0;
   $level_count = array();
   $menu_data = array();
   for ($i=0; $i<6; $i++)
   {
      $level_count[$i] = 0;
   }
   foreach ($menu as $key => $row) {
      if ( ! empty($menu[$key]['level'])) {
         // reverse level numbers...
         $menu[$key]['level'] = $level - $menu[$key]['level'];
         // ... and turn off items below desired root level
         // except for the direct line to the root.
         if (($menu[$key]['level'] < $root_level)
          && (!(in_array($key, $direct_line)))) {
            $menu[$key]['use'] = false;
         }
         if (in_array($key, $direct_line) && $hilitepath == true) {
            $menu[$key]['hilite'] = true;
         }
      }
      // create a new array with just the needed elements
      if ($menu[$key]['use'] == true) {
         if ($key == $item_key) {
            $menu_data[$count]['this_page'] = true;
         } else {
            $menu_data[$count]['this_page'] = false;
         }
         $menu_data[$count]['sec_name'] = $menu[$key]['sec_name'];
         $menu_data[$count]['link'] = $menu[$key]['link'];
         $menu_data[$count]['level'] = $menu[$key]['level'];
         $menu_data[$count]['hilite'] = $menu[$key]['hilite'];
         $level_count[$menu[$key]['level']]++;
         $count = $count + 1;
      }
   }

//   echo "<pre>"; print_r($menu_data); echo "</pre>";

   $t = new HCG_Smarty;

   $t->assign("menu_table", $menu_data);
   $t->assign("level_count", $level_count);

   $t->setTplPath($tpl);
   return $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: site_map
//!  need to add "level" info so indenting can happen.
//
// ------------------------------------------------------------------------

function site_map($db_source = "flat", $tpl = "site_map.tpl")
{
   if ($db_source == "flat") {
      $site_data = _getMenuDataFlat();
   } else {
      $site_data = _getMenuDataDb();
   }

   // determine what level each item is on.
   $count = 0;
   reset($site_data);
   while (list($key, $row) = each($site_data)) {
      if ($site_data[$key]['parent'] == "root") {
         $site_data[$key]['level'] = 1;
         $level_ref[$site_data[$key]['sec_id']] = 1;
         $site_map[$count] = $site_data[$key];
         $count = $count + 1;
      } elseif (!empty($site_data[$key]['parent'])) {
         $site_data[$key]['level'] = $level_ref[$site_data[$key]['parent']] + 1;
         $level_ref[$site_data[$key]['sec_id']] = $level_ref[$site_data[$key]['parent']] + 1;
         $site_map[$count] = $site_data[$key];
         $count = $count + 1;
      }
   }

   $t = new HCG_Smarty;

   $t->assign("site_data", $site_map);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// _getMenuDataFlat()
//
// ------------------------------------------------------------------------

function _getMenuDataFlat()
{
   global $_HCG_GLOBAL;
   $menu_file = $_HCG_GLOBAL['doc_root_dir'] . "/inc/" .
                 $_HCG_GLOBAL['site_id'] . "_menu.txt";
   $handle = fopen($menu_file, "r") or die ("Unable to open menu file");
   $count = 0;
   while ( ! feof($handle))
   {
      $line = fgets($handle, 1024);
      $line_array = explode("\t", $line);
      $parent = $line_array[0];
      $sec_id = $line_array[1];
      $sec_name = (isset($line_array[2])) ? $line_array[2] : '';
      $link = (isset($line_array[3])) ? $line_array[3] : '';
      $sort = (isset($line_array[4])) ? $line_array[4] : '';

      $menu_data[$count]['parent'] = trim($parent);
      $menu_data[$count]['sec_id'] = trim($sec_id);
      $menu_data[$count]['sec_name'] = trim($sec_name);
      $menu_data[$count]['link'] = trim($link);
      $menu_data[$count]['sort'] = trim($sort);
      $menu_data[$count]['level'] = "";
      $menu_data[$count]['use'] = false;
      $menu_data[$count]['hilite'] = false;
      $count = $count + 1;
   }
//   echo "<pre>"; print_r($menu_data); echo "</pre>";
   return $menu_data;
}


// ------------------------------------------------------------------------
// _getMenuDataDb
//   This gets the menu data specifically from the menu table used by the
//   admin website. It should probably be generalized to handle any site's
//   menu.
//
// ------------------------------------------------------------------------
function _getMenuDataDb()
{
   global $pcConfig;

   $query = "SELECT * ".
            "FROM ".addslashes($pcConfig['dbPrefix'])."aa__menu ".
            "ORDER BY sort";
   $results = pcdb_select($query);
//   echo "<pre>menu results: ".$query; print_r($results); echo "</pre>";
   for ($i=0; $i<count($results); $i++) {
      $menu_data[$i]['parent'] = $results[$i]['parent'];
      $menu_data[$i]['sec_id'] = $results[$i]['itemId'];
      $menu_data[$i]['sec_name'] = $results[$i]['shortlbl'];
      $menu_data[$i]['link'] = $results[$i]['link'];
      $menu_data[$i]['sort'] = $results[$i]['sort'];
      $menu_data[$i]['level'] = "";
      $menu_data[$i]['use'] = false;
      $menu_data[$i]['hilite'] = false;
   }
   return $menu_data;
}



// ------------------------------------------------------------------------
// TAG: site_map_fr
//!  need to add "level" info so indenting can happen.
//
// ------------------------------------------------------------------------

function site_map_fr($db_source = "flat", $tpl = "site_map.tpl")
{
   if ($db_source == "flat") {
      $site_data = _getMenuDataFlat_fr();
   } else {
      $site_data = _getMenuDataDb();
   }

   // determine what level each item is on.
   $count = 0;
   reset($site_data);
   while (list($key, $row) = each($site_data)) {
      if ($site_data[$key]['parent'] == "root") {
         $site_data[$key]['level'] = 1;
         $level_ref[$site_data[$key]['sec_id']] = 1;
         $site_map[$count] = $site_data[$key];
         $count = $count + 1;
      } elseif (!empty($site_data[$key]['parent'])) {
         $site_data[$key]['level'] = $level_ref[$site_data[$key]['parent']] + 1;
         $level_ref[$site_data[$key]['sec_id']] = $level_ref[$site_data[$key]['parent']] + 1;
         $site_map[$count] = $site_data[$key];
         $count = $count + 1;
      }
   }

   $t = new HCG_Smarty;

   $t->assign("site_data", $site_map);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// _getMenuDataFlat()
//
// ------------------------------------------------------------------------

function _getMenuDataFlat_fr()
{
   global $_HCG_GLOBAL;
   $menu_file = $_HCG_GLOBAL['doc_root_dir'] . "/inc/" .
                 $_HCG_GLOBAL['site_id'] . "_fr_menu.txt";
   $handle = fopen($menu_file, "r") or die ("Unable to open menu file");
   $count = 0;
   while (!feof($handle)) {
      $line = fgets($handle, 1024);
      $sort = "";
      //list($parent, $sec_id, $sec_name, $link, $sort) = explode("\t", $line);
      list($parent, $sec_id, $sec_name, $link) = explode("\t", $line);
      $menu_data[$count]['parent'] = $parent;
      $menu_data[$count]['sec_id'] = $sec_id;
      $menu_data[$count]['sec_name'] = $sec_name;
      $menu_data[$count]['link'] = $link;
      $menu_data[$count]['sort'] = $sort;
      $menu_data[$count]['level'] = "";
      $menu_data[$count]['use'] = false;
      $menu_data[$count]['hilite'] = false;
      $count = $count + 1;
   }
//   echo "<pre>"; print_r($menu_data); echo "</pre>";
   return $menu_data;
}

// ------------------------------------------------------------------------
// TAG: left_menu_db
//
// ------------------------------------------------------------------------

function left_menu_db($item_id, $root_level = 1, $hilitepath = false, $tpl = 'left_menu.tpl', $src_data = array())
{
   if (empty($src_data)) {
      $menu = _getMenuDataDb();
   } else {
      $menu = $src_data;
   }

   // we start with level 1 as the level of the actual item, then we
   // reverse levels later...
   $level = 1;
   $item = $item_id;
   $direct_line = array();
   while ($item != 0) { // 0 is the parent of the root node.
      foreach ($menu as $key => $row) {
         // mark all the sub items of the requested menu item. Each time
         // through the loop, this gets all the children, peers, and
         // parents in this branch of the tree, up to the root level.
         if ($menu[$key]['parent'] == $item) {
            $menu[$key]['level'] = $level;
            $menu[$key]['use'] = true;
         }
         // somewhere in this process we'll find the desired menu item.
         // when we do, we make a note of it's position in the array.
         if ($menu[$key]['sec_id'] == $item_id) {
            $item_key = $key;
            $menu[$key]['hilite'] = true;
         }
         // get the parent for next time through 'while' loop
         if ($menu[$key]['sec_id'] == $item) {
            $direct_line[] = $key;
            $next_item = $menu[$key]['parent'];
         }
      }
      $item = $next_item;
      $level = $level + 1;
   }

//   echo "<pre>menu results: ".$query; print_r($menu); echo "</pre>";

   $count = 0;
   foreach ($menu as $key => $row) {
      if (!empty($menu[$key]['level'])) {
         // reverse level numbers...
         $menu[$key]['level'] = $level - $menu[$key]['level'];
         // ... and turn off items below desired root level
         // except for the direct line to the root.
         if (($menu[$key]['level'] < $root_level)
          && (!(in_array($key, $direct_line)))) {
            $menu[$key]['use'] = false;
         }
         if (in_array($key, $direct_line) && $hilitepath == true) {
            $menu[$key]['hilite'] = true;
         }

      }
      // create a new array with just the needed elements
      if ($menu[$key]['use'] == true) {
         if ($key == $item_key) {
            $menu_data[$count]['this_page'] = true;
         } else {
            $menu_data[$count]['this_page'] = false;
         }
         $menu_data[$count]['sec_name'] = $menu[$key]['sec_name'];
         $menu_data[$count]['link'] = $menu[$key]['link'];
         $menu_data[$count]['level'] = $menu[$key]['level'];
         $menu_data[$count]['hilite'] = $menu[$key]['hilite'];
         $count = $count + 1;
      }
   }

//   echo "<pre>"; print_r($menu_data); echo "</pre>";

   $t = new HCG_Smarty;

   $t->assign("menu_table", $menu_data);

   $t->setTplPath($tpl);
   return $t->fetch($tpl);
}



// ------------------------------------------------------------------------
// TAG: page_info
//
// ------------------------------------------------------------------------
function page_info()
{
   global $_HCG_GLOBAL;

   // assemble full URL
   $page = $_SERVER['PHP_SELF'];

   // parse URL using PEAR:Net:URL

   // remove session in query if applicable

   // if filename == "", filename = index.php

   // reassemble URL with query in alphabetical order
   // and without any anchor text

   // search for fixed URL in database
   // this assumes that the URLs there are normalized.
   // I may need to add a function in the add menu item
   // function that performs the normalization.

   // assign data to $results array

   // Once page is identified, get subtree and build data structure
   // This should be done using the modified preorder tree traversal
   // described in that article. We'll assume that either I figure out
   // how to make updates to that system directly, or I will run the
   // rebuild_tree() function after each update.

   // call the left_menu.tpl and put results in $results[0]

   // return $results

}


?>
