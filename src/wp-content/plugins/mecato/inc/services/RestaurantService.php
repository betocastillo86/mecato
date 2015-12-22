<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/21/2015
 * Time: 11:01 PM
 */
class RestaurantService
{
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
}