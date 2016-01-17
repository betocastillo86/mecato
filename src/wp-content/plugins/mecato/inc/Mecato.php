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

    protected $viewDetailsRestaurant = null;
    protected $viewDetailsMenu = null;
    protected $viewSearch = null;


    protected $apiRestaurant = null;
    protected $apiMenu = null;

    function __construct()
    {
        $this->viewEditRestaurant = new EditRestaurantView();
        $this->viewEditMenu = new EditMenuView();

        $this->viewDetailsRestaurant = new DetailsRestaurantView();
        $this->viewDetailsMenu = new DetailsMenuView();
        $this->viewSearch = new SearchRestaurantView();

        $this->apiRestaurant = new ApiRestaurant();
        $this->apiMenu = new ApiMenu();


        //$this->viewEditMenu = new BikeDeliveryApi();

        add_action('wp_head', array($this, 'add_main_js'));

        add_filter('the_content', array($this, 'addShortcode'));

        add_action('init', array($this, 'add_rules'));


    }


    function add_rules()
    {
        //Reescribe la regla para buscador
        add_rewrite_rule('^buscar-restaurantes/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?', 'index.php?pagename=buscar-restaurantes&city=$matches[1]&text=$matches[2]&menuType=$matches[3]&lat=$matches[4]&lon=$matches[5]&zoom=$matches[6]', 'top');
    }

    function add_main_js()
    {
        ?>
        <script data-main="<?php echo MECATO_PLUGIN_URL . 'inc/js/mecato/' . MECATO_JS_BACKBONE . '.js' ?>"
                src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.1.20/require.min.js" async></script>
        <?php
        wp_deregister_script('jquery-ui-core');
        wp_deregister_script('dazzling-bootstrapjs');

        $this->add_main_css();
    }

    function add_main_css()
    {
        wp_enqueue_style("mecatocss", MECATO_PLUGIN_URL . 'inc/css/mecato.css');
    }

    /***
     * Agrega el shortcode para mostrar detalle dependiendo del tipo de contenido
     * @param $content
     * @return string
     */
    function addShortcode($content)
    {
        if(is_single())
        {
            switch (get_post_type())
            {
                case 'restaurante':
                    return '[mecato_restaurant_details]';
                case 'plato':
                    return '[mecato_menu_details]';
                default:
                    return $content;
            }
        }
        return $content;
    }

}