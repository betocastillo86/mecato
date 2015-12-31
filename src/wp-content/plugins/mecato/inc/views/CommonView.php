<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/30/2015
 * Time: 10:24 PM
 */
class CommonView
{
    /***
     * Muestra la galeria de las imagenes
     * @param $images ArrayObject Listado de imagnees
     */
    function show_gallery($images)
    {
        if(count($images)> 0)
        {
            $ids = "";
            for($i=0;$i<count($images);$i++)
            {
                if(strlen($ids)>0)
                    $ids .=",";
                $ids .= $images[$i];
            }

            echo do_shortcode('[gallery ids="'.$ids.'"]');
        }
    }
}