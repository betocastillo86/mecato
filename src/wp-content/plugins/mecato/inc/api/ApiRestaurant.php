<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/21/2015
 * Time: 10:27 PM
 */
require_once(ABSPATH . 'wp-content\plugins\rest-api\lib\endpoints\class-wp-rest-controller.php');
class ApiRestaurant extends WP_REST_Controller
{
    function __construct()
    {
        $this->register_routes();
    }

    function register_routes()
    {
        add_action( 'rest_api_init', function () {
            register_rest_route( 'api', '/restaurants', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_items') ,
            ) );
        } );

    }

    public function get_items($request)
    {
        //$p = "";
    }

}