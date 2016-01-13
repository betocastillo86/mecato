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
                'args' => array(
                    'cityId' => array(
                        'validate_callback' => 'absint',
                        'required' => true
                    ),
                    'menuType' => array(
                        'validate_callback' => function($value){
                            return $value == MECATO_PLUGIN_PAGE_TAX_VEGAN || $value == MECATO_PLUGIN_PAGE_TAX_VEGETARIAN || $value == "";
                        },
                        'required' => false
                    )
                )
            ) );

            register_rest_route( 'api', '/restaurants/(?P<id>\d+)/images', array(
                'methods' => 'POST',
                'callback' => array( $this, 'upload_images')
            ) );

        } );

    }

    /***
     * @param WP_REST_Request $request
     */
    public function get_items($request)
    {
        $restaurantService = new RestaurantService();

        $params = array('cityId' => $request->get_param('cityId'));

        if(strlen($request->get_param('menuType')) > 0)
            $params['menuType'] = $request->get_param('menuType');

        //La consulta debe ser de más de tres parametros
        if(strlen($request->get_param('text')) > 3)
            $params['text'] = $request->get_param('text');

        $restaurants = $restaurantService->getRestaurants($params);

        return $restaurants;
    }

    /***
     * Relaciona una imagen a un post
     * @param $request WP_REST_Request
     */
    public function upload_images($request)
    {
        require_once(MECATO_PLUGIN_DIR . 'inc/services/ImageService.php');
        $imageService= new ImageService();

        $id = $request['id'];
        try
        {
            $response = $imageService->addImage('file', $id, 'restaurante');

            if(gettype($response))
            {
                return array('id'=> $id );
            }
            else
            {
                return new WP_Error( $response->get_error_code(), $response->get_error_message(), array( 'status' => 404 ) );
            }
        }
        catch(Exception $ex)
        {
            return new WP_Error( $ex->getCode(), $ex->getMessage(), array( 'status' => 501 ) );
        }
    }

}