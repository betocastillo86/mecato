<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/21/2015
 * Time: 11:01 PM
 */
class RestaurantService
{
    private $postTypeRestaurant = 'restaurante';

    /****
     * Crea un nuevo restaruante
     * @param $restaurant Restaurant datos del restaurante
     * @return Restaurant con id
     */
    function insertRestaurant($restaurant)
    {

        $post = array(
            'post_title' => $restaurant->name,
            'post_content' => $restaurant->name,
            'post_status' => 'publish',
            'post_author' => $restaurant->userId,
            'post_type' => 'restaurante'
        );

        $restaurant->id = wp_insert_post($post);

        if(isset($restaurant->schedule))
           update_post_meta($restaurant->id, 'wpcf-schedule',$restaurant->schedule);
        if(isset($restaurant->address))
           update_post_meta($restaurant->id, 'wpcf-address',$restaurant->address);
        if(isset($restaurant->phone))
            update_post_meta($restaurant->id, 'wpcf-phone',$restaurant->phone);

        update_post_meta($restaurant->id, 'wpcf-lat',$restaurant->lat);
        update_post_meta($restaurant->id, 'wpcf-lon',$restaurant->lon);

        return $restaurant;
    }


    /***
     * Consulta la información de un restaurante
     * @param $id int id por el que debe consultar
     * @return Restaurant Información del restaurante
     */
    function getRestaurantById($id)
    {
        $post = get_post($id);
        if(isset($post) && $post->post_type == $this->postTypeRestaurant)
        {
            $model = new Restaurant();
            $model->id = $post->ID;
            $model->name = $post->post_title;

            $customFields =  get_post_custom($id);
            $model->address = $customFields['wpcf-address'][0];
            $model->schedule = $customFields['wpcf-schedule'][0];
            $model->lat = $customFields['wpcf-lat'][0];
            $model->lon = $customFields['wpcf-lon'][0];
            $model->phone = $customFields['wpcf-phone'][0];
            $model->userId = $post->post_author;

            return $model;
        }
        else
            return null;

    }
}