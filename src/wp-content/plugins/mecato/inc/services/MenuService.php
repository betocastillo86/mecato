<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/25/2015
 * Time: 10:27 AM
 */
class MenuService
{
    private $postTypeMenu = 'plato';

    /****
     * Crea un nuevo plato
     * @param $menu Menu datos del plato
     * @return Menu con id
     */
    function insertMenu($menu)
    {

        $post = array(
            'post_title' => $menu->name,
            'post_content' => $menu->description,
            'post_status' => 'publish',
            'post_author' => $menu->userId,
            'post_type' => $this->postTypeMenu//,
            //'post_parent' => $menu->restaurantId
        );

        $menu->id = wp_insert_post($post);

        //actualiza los campos adicionales
        $this->updateFields($menu, true);

        return $menu;
    }

    /***
     * Retorna los platos que sirven en un restaurante
     * @param $restaurantId id del restaurante
     * @return array Menu
     */
    function getMenuByRestaurantId($restaurantId)
    {
        $meta_query_args = array(
            'post_type' => 'plato',
            'numberposts' => -1,
            'meta_key' => '_wpcf_belongs_restaurante_id',
            'meta_value' => $restaurantId,
            'post_status' => 'publish'
        );

        $menus = array();
        $posts = get_posts( $meta_query_args );

        for($i = 0; $i < count($posts) ; $i++)
        {
            array_push($menus, $this->prepareModel($posts[$i]));
        }

        return $menus;
    }

    /****
     * Actualiza los datos de un plato
     * @param $menu Menu
     */
    function updateMenu($menu)
    {
        $post = array(
            'ID' => $menu->id,
            'post_title' => $menu->name,
            'post_content' => $menu->description,
        );

        wp_update_post($post);

        $this->updateFields($menu, false);

        return $menu;
    }

    /****
     * Actualiza los datos adicionales al post de un plato
     * @param $menu Menu datos del plato
     * @param $updateRestaurant boolean True: Actualiza la relacion con el restaurante
     */
    private function updateFields($menu, $updateRestaurant)
    {
        if(isset($menu->price))
            update_post_meta($menu->id, 'wpcf-precio',$menu->price);

        //update_post_meta($menu->id, 'wpcf-menuType',$menu->menuType);

        //Actualiza los ingredientes
        wp_set_post_terms($menu->id, $menu->topics, 'ingrediente');
        wp_set_post_terms($menu->id, $menu->menuType, 'tipo-de-plato');

        if($updateRestaurant)
            update_post_meta($menu->id, '_wpcf_belongs_restaurante_id',$menu->restaurantId);
    }

    /***
     * @param $id
     * @return Menu|null Retorna la informaci�n de un plato especifico
     */
    function getMenuById($id = null)
    {
        $post = get_post($id);
        if(isset($post) && $post->post_type == $this->postTypeMenu)
        {
            return $this->prepareModel($post);
        }
        else
            return null;
    }

    /****
     * Crea el modelo del plato desde el plan
     * @param $post  WP_Post
     * @return Menu
     */
    private function prepareModel($post)
    {
        $id = $post->ID;
        $model = new Menu();
        $model->id = $id;
        $model->name = $post->post_title;
        $model->description = $post->post_content;

        $customFields =  get_post_custom($post->ID);
        $model->price = $customFields['wpcf-precio'][0];
        $model->restaurantId = $customFields['_wpcf_belongs_restaurante_id'][0];
        $model->images = $customFields['wpcf-ids-imagenes'];

        $model->topics = wp_get_post_terms($id, 'ingrediente');
        $model->menuType = wp_get_post_terms($id, 'tipo-de-plato')[0];

        $model->userId = $post->post_author;
        $model->guid = $post->guid;

        return $model;
    }
}