<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/27/2015
 * Time: 11:06 AM
 */
require_once(ABSPATH . 'wp-content\plugins\rest-api\lib\endpoints\class-wp-rest-controller.php');
class ApiMenu
{
    function __construct()
    {
        $this->register_routes();
    }

    function register_routes()
    {
        add_action( 'rest_api_init', function () {

            register_rest_route( 'api', '/menus/(?P<id>\d+)/images', array(
                'methods' => 'POST',
                'callback' => array( $this, 'upload_images') ,
            ) );


        } );

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
            $response = $imageService->addImage('file', $id, 'plato');

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