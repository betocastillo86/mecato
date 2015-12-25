<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/25/2015
 * Time: 10:26 AM
 */

require_once(MECATO_PLUGIN_DIR . 'inc/services/RestaurantService.php');
require_once(MECATO_PLUGIN_DIR . 'inc/services/MenuService.php');

class EditMenuView
{
    /**
     * @var RestaurantService restaurante
     */
    private $restaurantService;

    /**
     * @var MenuService menu
     */
    private $menuService;

    function __construct()
    {
        add_shortcode('mecato_edit_menu', array($this, 'show_view'));
    }


    function show_view($attr)
    {
        if (!isset($_REQUEST['restId']))
            return;


        $this->menuService = new MenuService();

        if (isset($_POST['menu_name'])) {
            $savedMenu = $this->save_menu();
            if (isset($savedMenu) && $savedMenu->id > 0) {
                $this->show_confirm_saved($savedMenu);
            }
        } else {
            //Si viene el id por querystring es editar y no muestra toda la información
            $isEdit = isset($_GET['id']);

            //Consulta el restaurante
            $menu = null;
            if ($isEdit) {
                $menu = $this->menuService->getMenuById($_GET['id']);
                //Si el restaurante no existe lo envia al home
                if (!isset($menu)) {
                    wp_redirect(home_url());
                    exit;
                }

            }


            ?>
            <div id="divMainSection">
                <form action="" method="post">
                    <div class="row">
                        <?php
                        $this->show_basic_order_form($menu);
                        ?>
                    </div>
                    <div id="divModalNewOrder"></div>
                </form>
            </div>

            <?php
        }


    }


    /***
     * Muestra la informaci?n b?sica del formulario para solicitar un servicio
     * @param $menu Menu datos del menu
     */
    function show_basic_order_form($menu)
    {

        wp_enqueue_style("mecatocss", MECATO_PLUGIN_URL . 'inc/css/mecato.css');

        ?>
        <div class="col-sm-12">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3 class="panel-title">Detalle del plato</h3>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="" class="col-md-6 control-label">Que tipo de plato es:</label>

                            <div class="col-md-6">
                                <input name="menu_type" type='hidden' value="Vegetariano"/>

                                <div class="btn-group" data-toggle="buttons">
                                    <button type="button" class="btn btn-default active" data-radio-name="menu_type">
                                        Vegetariano
                                    </button>
                                    <button type="button" class="btn btn-default" data-radio-name="menu_type">Vegano
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" data-valfor="name">
                        <div class="form-group required">
                            <label for="menu_name" class="control-label">Nombre</label>
                            <input id="menu_name" name="menu_name" type="text" data-var="menu_name"
                                   class="form-control required" maxlength="50"
                                   value="<?php echo $menu->name ?>"/>
                        </div>
                    </div>
                    <div class="col-xs-12" data-valfor="description">
                        <div class="form-group">
                            <label for="menu_description" class="control-label">Descripción</label>
                            <textarea id="menu_description" name="menu_description" type="text"
                                      data-var="menu_description"
                                      class="form-control required"
                                      maxlength="500"><?php echo $menu->description ?></textarea>
                        </div>
                    </div>
                    <div class="col-xs-12" data-valfor="price">
                        <div class="form-group">
                            <label for="menu_price" class="control-label">Precio</label>
                            <input id="menu_price" name="menu_price" type="text" data-var="menu_price"
                                   class="form-control required" data-val="int" maxlength="50"
                                   value="<?php echo $menu->price ?>"/>
                        </div>
                    </div>
                    <div class="col-xs-12" data-valfor="topics">
                        <div class="form-group">
                            <label for="menu_price" class="control-label">Escoge algunos de los ingredientes del
                                plato:</label>
                        </div>
                    </div>

                    <?php $this->show_topics(); ?>


                    <?php
                    /*if ($restaurant != null) {
                        $this->show_edit_form_fields($restaurant);
                    }*/
                    ?>

                    <div class="col-sm-offset-2 col-sm-12">
                        <p><input type="button" id="btnNewService" class="btn btn-lg btn-success" role="button"
                                  value="<?php echo($menu != null ? "Actualizar plato" : "Guardar plato") ?>"/>
                        </p>

                    </div>
                </div>
            </div>
        </div>
        <style>
            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }
        </style>

        <?php
    }

    function show_topics()
    {
        //$topics = get_taxonomies(array('name' => 'ingrediente' ));
        $topics = get_terms('ingrediente', array('hide_empty' => false));

        for($i = 0; $i < count($topics); $i++ )
        {
            $topic = $topics[$i];
            ?>
            <div class="col-xs-12" data-valfor="topics">
                <div class="form-group">
                    <label for="" class="col-md-6 control-label"><?php echo  $topic->name ?></label>

                    <div class="col-md-6">
                        <input name="menu_type" type='hidden' value="Si"/>
                        <div class="btn-group" data-toggle="buttons">
                            <button type="button" class="btn btn-default" data-radio-name="menu_topic_<?php echo $topic->term_id ?>">
                                Si
                            </button>
                            <button type="button" class="btn btn-default active" data-radio-name="menu_topic_<?php echo $topic->term_id ?>">No
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <?php
        }

    }

}