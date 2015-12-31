<?php
/**
 * Plugin Name: Mecato
 * Author: Gabriel Castillo
 * Version: 0.0.1
 * Description: Plugin para encontrar restaurante vegetariano
 */


if (defined('URE_PLUGIN_URL')) {
    wp_die('It seems that other version of User Role Editor is active. Please deactivate it before use this version');
}

define("MECATO_PLUGIN_URL", plugin_dir_url(__FILE__));
define("MECATO_PLUGIN_DIR", plugin_dir_path(__FILE__));

define("MECATO_PLUGIN_PAGE_CREATE_REST", 44);
define("MECATO_PLUGIN_PAGE_EDIT_REST", 82);
define("MECATO_PLUGIN_PAGE_CREATE_MENU", 112);

define("MECATO_PLUGIN_PAGE_TAX_VEGETARIAN", 'vegetariano');
define("MECATO_PLUGIN_PAGE_TAX_VEGAN", 'vegano');

define("MECATO_JS_BACKBONE", 'main');

require_once('inc/Mecato.php');

/**Models**/
require_once('inc/models/Restaurant.php');
require_once('inc/models/Menu.php');

/**Views**/

require_once('inc/views/EditRestaurantView.php');
require_once('inc/views/EditMenuView.php');
require_once('inc/views/DetailsRestaurantView.php');

/**Api**/
require_once('inc/api/ApiRestaurant.php');
require_once('inc/api/ApiMenu.php');

$GLOBALS['mecato'] = new Mecato();