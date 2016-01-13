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
     * Consulta la lista de restaurantes según el filtro
     * @param $filter Filtro que se aplicará a la consulta
     *          cityId : Ciudad a filtrar
     *          menuTypeId : Tipo de menu que venden en el restaurante
     *          name: Nombre del restaurante que se intenta consultar
     * @return Array listado de restaurante encontrados de acuerdo al filtro
     */
    function getRestaurants($filter)
    {
        $query =
            array(
                'post_type' => 'restaurante',
                'numberposts'=> -1,
                'meta_query' => array(),
                'tax_query' => array(),
            );


        if(isset($filter['text']) && strlen($filter['text']) > 0)
            $query['s'] = $filter['text'];


        //Hace push de la consulta por id de ciudad en taxonomy
        if(isset($filter['cityId']))
        {
            array_push($query['tax_query'],
                array(
                    'taxonomy' =>  'ciudad',
                    'field'=> 'slug',
                    'terms' => $filter['cityId']));
        }

        //Filtra por tipo de menu
        if(isset($filter['menuType']))
        {
            $keyFilter = $filter['menuType'] == MECATO_PLUGIN_PAGE_TAX_VEGETARIAN ? 'vegetarianos' : 'veganos';
            array_push($query['meta_query'],
                array(
                    'key' => 'wpcf-num-platos-'.$keyFilter,
                    'value' => 0,
                    'compare' => '>'
                ));
        }

        $posts = get_posts($query);
        $models = array();

        foreach($posts as $post)
        {
            array_push($models, $this->postToModel($post));
        }

        return $models;
    }


    /****
     * Crea un nuevo restaruante
     * @param $restaurant Restaurant datos del restaurante
     * @return Restaurant con id
     */
    function insertRestaurant($restaurant)
    {

        $post = array(
            'post_title' => $restaurant->name,
            'post_content' => $restaurant->description,
            'post_status' => 'publish',
            'post_author' => $restaurant->userId,
            'post_type' => 'restaurante'
        );

        $restaurant->id = wp_insert_post($post);

        $this->updateFields($restaurant, true);

        return $restaurant;
    }

    /****
     * Actualiza los datos de un restaurante
     * @param $restaurant Restaurant
     */
    function updateRestaurant($restaurant)
    {
        $post = array(
            'ID' => $restaurant->id,
            'post_title' => $restaurant->name,
            'post_content' => $restaurant->description,
        );

        wp_update_post($post);

        $this->updateFields($restaurant, false);

        return $restaurant;
    }

    /****
     * Actualiza los datos adicionales al post de un restaurante
     * @param $restaurant Restaurant datos del restaurante
     * @param $isNew True: es creación
     */
    private function updateFields($restaurant, $isNew)
    {
        if(isset($restaurant->schedule))
            update_post_meta($restaurant->id, 'wpcf-schedule',$restaurant->schedule);
        if(isset($restaurant->address))
            update_post_meta($restaurant->id, 'wpcf-address',$restaurant->address);
        if(isset($restaurant->phone))
            update_post_meta($restaurant->id, 'wpcf-phone',$restaurant->phone);

        update_post_meta($restaurant->id, 'wpcf-lat',$restaurant->lat);
        update_post_meta($restaurant->id, 'wpcf-lon',$restaurant->lon);


        //Actualiza el numero de platos a 0
        if($isNew)
        {
            update_post_meta($restaurant->id, 'wpcf-num-platos-vegetarianos',0);
            update_post_meta($restaurant->id, 'wpcf-num-platos-veganos',0);
        }


        wp_set_post_terms($restaurant->id, $restaurant->city, 'ciudad');
    }


    /***
     * Actualiza el numero de platos vegetarianos y veganos por restaurante
     * @param $restaurantId Actualiza el numero de platos por restaurante
     */
    public function updateNumMenuByType($restaurantId)
    {
        //base de la consulta de menu
        $baseQuery = array(
            'post_type' => 'plato',
            'numberposts' => -1,
            'meta_key' => '_wpcf_belongs_restaurante_id',
            'meta_value' => $restaurantId,
            'tax_query' => array(
                array(
                    'taxonomy' => 'tipo-de-plato',
                    'field' => 'slug',
                    'terms' => MECATO_PLUGIN_PAGE_TAX_VEGAN,
                )
            )
        );

        //Consulta los platos veganos
        $countVegan = count(get_posts($baseQuery)) ;

        //Actualiza la consulta para consultar los platos vegetarianos
        $baseQuery['tax_query'][0]['terms'] = MECATO_PLUGIN_PAGE_TAX_VEGETARIAN;
        $countVegetarian = count(get_posts($baseQuery)) ;

        //Actualiza los datos del plato
        update_post_meta($restaurantId, 'wpcf-num-platos-vegetarianos',$countVegetarian);
        update_post_meta($restaurantId, 'wpcf-num-platos-veganos',$countVegan);

    }


    /***
     * Consulta la información de un restaurante
     * @param $id int id por el que debe consultar
     * @return Restaurant Información del restaurante
     */
    function getRestaurantById($id = null)
    {
        $post = get_post($id);
        if(isset($post) && $post->post_type == $this->postTypeRestaurant)
        {
            return $this->postToModel($post, true, 0);
        }
        else
            return null;

    }

    /***
     * Toma un post y lo convierte en un modelo
     * @param $post WP_Post información del post
     * @param bool|true $addMeta true:agrega información meta del post
     * @param $sizeThumbnail int tamaño de la imagen que se va mosotrar. Si el valor es igual a 0 no la carga
     * @return Restaurant Modelo con la infromación del restauante
     */
    private function postToModel($post, $addMeta = true, $sizeThumbnail = 250)
    {
        $model = new Restaurant();
        $model->id = $post->ID;
        $model->name = $post->post_title;
        $model->description = $post->post_content;
        $model->userId = $post->post_author;
        $model->guid = $post->guid;

        if($addMeta)
        {
            $customFields =  get_post_custom($post->ID);
            $model->address = $customFields['wpcf-address'][0];
            $model->schedule = $customFields['wpcf-schedule'][0];
            $model->lat = $customFields['wpcf-lat'][0];
            $model->lon = $customFields['wpcf-lon'][0];
            $model->phone = $customFields['wpcf-phone'][0];
            $model->images = $customFields['wpcf-ids-imagenes'];

            $model->numVegan = $customFields['wpcf-num-platos-veganos'][0];
            $model->numVegetarian = $customFields['wpcf-num-platos-vegetarianos'][0];


            //Carga el modelo de la ciudad
            $city = wp_get_post_terms($post->ID, 'ciudad')[0];
            $model->city = array(
                'term_id' => $city->term_id,
                'name' => $city->name,
                'slug'=> $city->slug);
        }

        //Carga la imagen pequeña
        if($sizeThumbnail > 0)
        {
           $model->thumbnail =  wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $sizeThumbnail );
            //Si no tiene imagen seleccionada por defecto, carga una imagen de la lista
            if(!$model->thumbnail && count($model->images) > 0)
            {
                $model->thumbnail =wp_get_attachment_image_src( $model->images[0])[0];
            }
        }

        return $model;
    }
}