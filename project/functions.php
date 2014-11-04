<?php
function myAutoloader($class) 
{
  $path = MAIN . "/src/{$class}.php";
  if(is_file($path)) {
    include($path);
  }
  else 
  {
    throw new Exception("Classfile '{$class}' at {$path} does not exists.");
  }
}
spl_autoload_register('myAutoloader');

function getQueryString($options, $prepend='?')
{
  $query = array();
  parse_str($_SERVER['QUERY_STRING'], $query);
  $query = array_merge($query, $options);
  return $prepend . http_build_query($query);
}


function getHitsPerPage($hits)
{
  $nav = "Träffar per sida: ";
  foreach($hits AS $val) {
    $nav .= "<a href='" . getQueryString(array('hits' => $val)) . "'>$val</a> ";
  }  
  return $nav;
}

function getPageNavigation($hits, $page, $max, $min=1)
{
  $nav  = "<a href='" . getQueryString(array('page' => $min)) . "'>&lt;&lt;</a> ";
  $nav .= "<a href='" . getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>&lt;</a> ";
  for($i=$min; $i<=$max; $i++)
  {
    $nav .= "<a href='" . getQueryString(array('page' => $i)) . "'>$i</a> ";
  }
  $nav .= "<a href='" . getQueryString(array('page' => ($page < $max ? $page + 1 : $max) )) . "'>&gt;</a> ";
  $nav .= "<a href='" . getQueryString(array('page' => $max)) . "'>&gt;&gt;</a> ";
  return $nav;
}
function orderby($column) 
{
	return "<span class='orderby'><a href='" . getQueryString(array('orderby'=>$column, 'order'=>'asc')) . "'>&darr;</a><a href='" . getQueryString(array('orderby'=>$column, 'order'=>'desc')) . "'>&uarr;</a></span>";
}



/**
 * Create a navigation bar / menu, with submenu.
 *
 * @param string $menu for the navigation bar.
 * @return string as the html for the menu.
 */
function get_navbar($menu) {
  // Keep default options in an array and merge with incoming options that can override the defaults.
  $default = array(
    'id'      => null,
    'class'   => null,
    'wrapper' => 'nav',
  );
  $menu = array_replace_recursive($default, $menu);
 
 
  // Create the ul li menu from the array, use an anonomous recursive function that returns an array of values.
  $create_menu = function($items, $callback) use (&$create_menu) {
    $html = null;
    $hasItemIsSelected = false;
 
    foreach($items as $item) {
 
      // has submenu, call recursivly and keep track on if the submenu has a selected item in it.
      $submenu        = null;
      $selectedParent = null;
      if(isset($item['submenu'])) {
        list($submenu, $selectedParent) = $create_menu($item['submenu']['items'], $callback);
        $selectedParent = $selectedParent ? " selected-parent" : null;
      }
 
      // Check if the current menuitem is selected
      $selected = $callback($item['url']) ? 'selected' : null;
      if($selected) {
        $hasItemIsSelected = true;
      }
      $selected = ($selected || $selectedParent) ? " class='${selected}{$selectedParent}' " : null;      
      $html .= "\n<li{$selected}><a href='{$item['url']}' title='{$item['title']}'>{$item['text']}</a>{$submenu}</li>\n";
    }
 
    return array("\n<ul>$html</ul>\n", $hasItemIsSelected);
  };
 
  // Call the anonomous function to create the menu, and submenues if any.
  list($html, $ignore) = $create_menu($menu['items'], $menu['callback']);
 
 
  // Set the id & class element, only if it exists in the menu-array
  $id      = isset($menu['id'])    ? " id='{$menu['id']}'"       : null;
  $class   = isset($menu['class']) ? " class='{$menu['class']}'" : null;
  $wrapper = $menu['wrapper'];
 
  return "\n<{$wrapper}{$id}{$class}>{$html}</{$wrapper}>\n";
}

?>