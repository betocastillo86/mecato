<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/19/2015
 * Time: 11:40 AM
 */
class Mecato
{


    protected $viewEditRestaurant = null;
    protected $viewEditMenu = null;

    function __construct()
    {
        $this->viewEditRestaurant = new EditRestaurantView();
        //$this->viewEditMenu = new BikeDeliveryApi();

        add_action('wp_head', array($this, 'add_main_js'));
    }

    function add_main_js()
    {
        ?>
        <script data-main="<?php echo MECATO_PLUGIN_URL.'inc/js/mecato/'.MECATO_JS_BACKBONE.'.js' ?>" src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.1.20/require.min.js" async ></script>
        <?php
    }
}