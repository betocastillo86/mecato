<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/24/2015
 * Time: 12:15 PM
 */
class ImageService
{
    /****
     * Asocia una imagen con un post
     * @param $fileId identificador del archivo
     * @param $postId id del post a relacionar
     * @param $postType tipo de post
     * @return bool|int|WP_Error true respuesta satisfactoria
     */
    function addImage($fileId, $postId, $postType)
    {
        include_once(ABSPATH . "wp-admin" . '/includes/image.php');
        include_once(ABSPATH . "wp-admin" . '/includes/file.php');
        include_once(ABSPATH . "wp-admin" . '/includes/media.php');


        $post = get_post($postId);
        $imageTitle = $post->post_title.the_date('yyyymmdd').the_time('hhmmss');
        $response = media_handle_upload($fileId, $postId, array(title => $imageTitle ));

        if(gettype($response) == 'integer')
        {
            $urlImage = get_post_meta($response, '_wp_attached_file');
            add_post_meta($postId, 'wpcf-imagenes', get_site_url() .'/wp-content/uploads/'. $urlImage[0]);
            return true;
        }
        else
        {
            return $response;
        }
    }
}