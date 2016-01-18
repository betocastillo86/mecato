<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 1/17/2016
 * Time: 4:55 PM
 */
class HomeView
{
    function __construct()
    {
        add_shortcode('mecato_service_home', array($this, 'show_service_circle'));
    }

    /****
     * Muestra los circulos que por defecto no se pueden mostrar
     */
    function show_service_circle($params)
    {
        ob_start();
        $image = $params['image'];
        $link = $params['link'];
        ?>
        <a href="<?php echo $link; ?>"><i class="pixeden" style="background:url(<?php echo $image; ?>) no-repeat center;width:100%; height:100%;"></i> </a>
        <?php
        $html=ob_get_contents();
        ob_end_clean();
        return $html;
    }
}