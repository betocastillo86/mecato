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
define("MECATO_JS_BACKBONE", 'main');

require_once('inc/Mecato.php');

/**Models**/
require_once('inc/models/Restaurant.php');

/**Views**/

require_once('inc/views/EditRestaurantView.php');

/**Api**/
require_once('inc/api/ApiRestaurant.php');

$GLOBALS['mecato'] = new Mecato();