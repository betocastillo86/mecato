<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/25/2015
 * Time: 10:26 AM
 */

require_once(MECATO_PLUGIN_DIR . 'inc/services/RestaurantService.php');
require_once(MECATO_PLUGIN_DIR . 'inc/services/MenuService.php');
require_once("CommonView.php");

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
                if (!isset($menu) || $menu->restaurantId != $_GET['restId']) {
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
     * Mapea y envia a guardar el plato
     * @return null|Menu Menu que fue guardado
     */
    function save_menu()
    {
        if (isset($_POST['menu_name']) && isset($_POST['menu_description'])
            && isset($_POST['menu_type'])
        ) {
            $model = new Menu();

            $model->name = $_POST['menu_name'];
            $model->menuType = $_POST['menu_type'];
            $model->description = $_POST['menu_description'];
            $model->userId = get_current_user_id();
            $model->restaurantId = $_GET['restId'];

            if (isset($_POST['menu_price']))
                $model->price = $_POST['menu_price'];

            $model->topics = $_POST['menu_topics'];


            //Actualiza o inserta el restaurante
            if (isset($_REQUEST['id'])) {
                $model->id = $_REQUEST['id'];
                return $this->menuService->updateMenu($model);
            } else {
                //Guarda el restaurante
                return $this->menuService->insertMenu($model);
            }


        } else {
            ?>
            <h2>Los datos son invalidos</h2>
            <?php
            return null;
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
                                <input id="menu_type" name="menu_type" type='hidden' value="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGETARIAN?>"/>

                                <div class="btn-group" data-toggle="buttons">
                                    <button type="button" class="btn btn-default <?php echo $menu->menuType->slug == 'vegano' ? 'inactive' : '' ?>" data-radio-name="menu_type" data-id="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGETARIAN?>">
                                        Vegetariano
                                    </button>
                                    <button type="button" class="btn btn-default <?php echo $menu->menuType->slug == 'vegetariano' ? 'inactive' : '' ?>" data-radio-name="menu_type" data-id="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGAN?>">Vegano
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

                    <?php $this->show_topics($menu); ?>


                    <?php
                    if ($menu != null) {
                        $this->show_edit_form_fields($menu);
                    }
                    ?>

                    <div class="col-sm-offset-2 col-sm-12">
                        <input type="button" id="btnNewService" class="btn btn-lg btn-success" role="button"
                                  value="<?php echo($menu != null ? "Actualizar plato" : "Guardar plato") ?>"/>
                        <a href="<?php  echo ($menu != null ? get_permalink($menu->id) : get_permalink($_GET['restId']))?>" class="btn btn-lg btn-default">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }

            .btn.inactive{
                background-color:#84C1AD !important;
            }
            .btn.focus{
                background-color:#1b926c !important;
            }

        </style>

        <?php
    }

    /***
     * Muestra todos los ingredientes posibles para el plato. Si es edicion los marca como seleccionados
     * @param $menu Menu datos del plato
     */
    function show_topics($menu)
    {
        //$topics = get_taxonomies(array('name' => 'ingrediente' ));
        $topics = get_terms('ingrediente', array('hide_empty' => false));


        //Retorna todos los tags seleccionados por el usuario previamente
        $topicsSelected = null;
        if($menu != null)
            $topicsSelected = array_map(function($arr_menu){ return $arr_menu->slug; }, $menu->topics);

        for($i = 0; $i < count($topics); $i++ )
        {
            $topic = $topics[$i];

            //Agrupa  los ingredientes de a 5 para que el usuario pueda ir descubriendo más si lo desea
            $divNum = intval( $i / 5);

            //Valida si el termino se selecciona o no
            $selected = $topicsSelected != null ? in_array($topic->slug, $topicsSelected) != null : false;
            ?>
            <div class="col-xs-12 listTopics" data-valfor="topics" data-div="<?php echo $divNum ?>"  <?php echo ($divNum > 0 ? 'style="display:none"' : '') ?>>
                <div class="form-group">
                    <label for="" class="col-md-6 control-label"><?php echo  $topic->name ?></label>

                    <div class="col-md-6">
                        <div class="btn-group" data-toggle="buttons">
                            <button type="button" class="btn btn-default yesTopic <?php echo (!$selected ? 'inactive' : '')?>" data-radio-name="menu_topic_<?php echo $topic->term_id ?>" data-id="<?php echo $topic->slug ?>">
                                Si
                            </button>
                            <button type="button" class="btn btn-default  <?php echo ($selected ? 'inactive' : '')?>" data-radio-name="menu_topic_<?php echo $topic->term_id ?>"  data-id="<?php echo $topic->slug ?>">No
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <?php
        }

        ?>
            <a id="aMoreTopics" href="javascript:void(0);"  >Mostrar más ingredientes</a>
            <input name="menu_topics" id="menu_topics" type="hidden" value=""  />
        <?php

    }

    function show_gallery($menu)
    {
        ?>
        <h3 class="entry-title">Imagenes de <?php echo $menu->name ?></h3>
        <?php
        if(count($menu->images) > 0)
        {
            $commonView = new CommonView();
            $commonView->show_gallery($menu->images);
        }
    }

    /***
     * Muestra el resto de la información
     * @param $menu Menu Información del plato a actualizar
     */
    function show_edit_form_fields($menu)
    {
        wp_enqueue_style("mecatocss_dropzone", MECATO_PLUGIN_URL . 'inc/css/dropzone.css');

        $this->show_gallery($menu);
        ?>
        <style>
            .dz-details{
                display: none !important;
            }
        </style>

        <div class="col-xs-12" id="images">
            <div class="dropzone" id="dropzoneForm">
                <div class="fallback">
                    <input name="file" type="file" multiple />
                    <input type="submit" value="Upload" />
                </div>
                <div class="dz-message">Selecciona o arrastra los archivos del plato</div>
            </div>
        </div>
        <div id="messageUploadOk" class="alert alert-success" role="alert" style="display:none">
            <strong>Muchas gracias!</strong> Los archivos fueron cargados correctamente.
        </div>
        <div id="messageUploadError" class="alert alert-error" role="alert" style="display:none">
            <strong>Muchas gracias!</strong> Los archivos fueron cargados correctamente.
        </div>

        <?php

    }

    /***
    * Informa al usuario que el plato quedó guardado
    * @param $menu Menu Información del plato guardado
    */
    function show_confirm_saved($menu)
    {
        ?>
        <div class="alert alert-success" role="alert">
            <strong>Muchas gracias!</strong> Fue guardado el plato
            <strong><a href="<?php echo get_permalink($menu->id) ?>"><?php echo $menu->name; ?></a> </strong>.
        </div>
        <div class="col-md-12 center-block">
            <p>
                Si conoces más platos de este restaurante, ayudanos a completarlos
            </p>
            <div style="text-align: center;">
                <a id="singlebutton" name="singlebutton" class="btn btn-xs btn-default " role="button"
                   href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_CREATE_MENU) ?>?restId=<?php echo $menu->restaurantId ?>">
                    Crear plato
                </a>
            </div>

            <?php
            if(!isset($_REQUEST['id']))
            {
                ?>
                <p>
                    Si conoces restaurantes donde hayan platos vegetarianos o veganos, ayudanos a completar la información aquí.
                </p>

                <div style="text-align: center;">
                    <a id="singlebutton" name="singlebutton" class="btn btn-xs btn-default " role="button"
                       href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_CREATE_REST) ?>">
                        Crear restaurante
                    </a>
                </div>
                <?php
            }
            else
            {
                ?>
                <div style="text-align: center;">
                    <a id="singlebutton" name="singlebutton" class="btn btn-xs btn-default " role="button"
                       href="<?php echo get_site_url() ?>">
                        Ir al inicio
                    </a>
                </div>
                <?php
            }
            ?>



        </div>
        <?php
    }

}