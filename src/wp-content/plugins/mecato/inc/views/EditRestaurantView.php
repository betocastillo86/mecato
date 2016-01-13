<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/19/2015
 * Time: 11:44 AM
 */
require_once(MECATO_PLUGIN_DIR . 'inc/services/RestaurantService.php');

class EditRestaurantView
{
    /**
     * @var RestaurantService restaurante
     */
    private $restaurantService;

    function __construct()
    {
        add_shortcode('mecato_edit_restaurant', array($this, 'show_view'));
    }


    function show_view($attr)
    {
        $this->restaurantService = new RestaurantService();

        if (isset($_POST['restaurant_name'])) {
            $savedRestaurant = $this->save_restaurant();
            if (isset($savedRestaurant) && $savedRestaurant->id > 0) {
                $this->show_confirm_saved($savedRestaurant);
            }
        } else {
            //Si viene el id por querystring es editar y no muestra toda la información
            $isEdit = isset($_GET['id']);

            //Consulta el restaurante
            $restaurant = null;
            if ($isEdit) {
                $restaurant = $this->restaurantService->getRestaurantById($_GET['id']);
                //Si el restaurante no existe lo envia al home
                if (!isset($restaurant)) {
                    wp_redirect(home_url());
                    exit;
                }

            }


            ?>
            <div id="divMainSection">
                <form action="" method="post">
                    <div class="row">

                        <?php
                        if (!$isEdit) {
                            $this->show_map();
                        }

                        $this->show_basic_order_form($restaurant);
                        ?>

                    </div>
                    <div id="divModalNewOrder"></div>
                </form>
            </div>

            <?php
        }


    }

    /***
     * Informa al usuario que el restaurante quedó guardado
     * @param $restaurant Restaurant Información del restaurante guardado
     */
    function show_confirm_saved($restaurant)
    {
        ?>
        <div class="alert alert-success" role="alert">
            <strong>Muchas gracias!</strong> Fue guardado el restaurante
            <strong><?php echo $restaurant->name; ?></strong>.
        </div>
        <div class="col-md-12 center-block">
            <p>
                Ayudanos a completar la información de <strong><?php echo $restaurant->name; ?></strong> creando un
                plato.
            </p>
            <a id="singlebutton" href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_CREATE_MENU).'?restId='.$restaurant->id ?>" class="btn btn-lg btn-primary center-block">
                Crear un plato
            </a>

            <?php
                if(!isset($_REQUEST['id']))
                {
                    ?>
                    <p>
                        Si conoces más información acerca de este restaurante ayudanos completandola aquí.
                    </p>

                    <div style="text-align: center;">
                        <a id="singlebutton" name="singlebutton" class="btn btn-xs btn-default " role="button"
                           href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_EDIT_REST) ?>?id=<?php echo $restaurant->id ?>">
                            Actualizar restaurante
                        </a>
                    </div>
                    <?php
                }
                else
                {
                    ?>
                    <div class="row">
                        <div style="text-align: center;">
                            <a id="singlebutton" name="singlebutton" class="btn btn-xs btn-default " role="button"
                               href="<?php echo $restaurant->guid ?>">
                                Ir al restaurante
                            </a>
                        </div>
                    </div>
                    <?php
                }
            ?>



        </div>
        <?php
    }

    /***
     * Mapea y envia a guardar el restauramnte
     * @return null|Restaurant Restaurante que fue guardado
     */
    function save_restaurant()
    {
        if (isset($_POST['restaurant_name']) && isset($_POST['restaurant_address'])
            && isset($_POST['restaurant_lat']) && isset($_POST['restaurant_lon'])
        ) {
            $model = new Restaurant();

            $model->name = $_POST['restaurant_name'];
            $model->address = $_POST['restaurant_address'];
            $model->schedule = $_POST['restaurant_schedule'];
            $model->lat = $_POST['restaurant_lat'];
            $model->lon = $_POST['restaurant_lon'];
            $model->description = $_POST['restaurant_description'];
            $model->city = $_POST['restaurant_city'];
            $model->userId = get_current_user_id();


            if (isset($_POST['restaurant_phone']))
                $model->phone = $_POST['restaurant_phone'];

            //Actualiza o inserta el restaurante
            if (isset($_REQUEST['id'])) {
                $model->id = $_REQUEST['id'];
                $model = $this->restaurantService->updateRestaurant($model);
            } else {
                //Guarda el restaurante
                $model = $this->restaurantService->insertRestaurant($model);
            }

            //consulta nuevamente el post guardado
            $post = get_post($model->id);
            $model->guid = $post->guid;

            return $model;
        } else {
            ?>
            <h2>Los datos son invalidos</h2>
            <?php
            return null;
        }
    }

    /***
     * Muestra el mapa con las funcionalidades
     */
    function show_map()
    {



        ?>


        <style>

            #map_location {
                height: 440px;
                padding: 20px;
                border: 2px solid #CCC;
                margin-bottom: 20px;
                background-color: #FFF
            }

            #map_location {
                height: 400px
            }

            @media all and (max-width: 991px) {
                #map_location {
                    height: 650px
                }
            }
        </style>
        <div class="col-sm-6">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3 class="panel-title">Ubicaci&oacute;n</h3>
                </div>

                <div class="panel-body">

                    <div id="map_location"></div>

                </div>
            </div>
        </div>
        <select id="restaurant_city" name="restaurant_city" style="display:none">
        <?php
        $cities = get_terms('ciudad', array('hide_empty' => false));

            foreach($cities as $city)
            {
                ?>
                <option value="<?php echo $city->slug ?>">
                    <?php echo $city->name ?>
                </option>
                <?php
            }

        ?>
        </select>

        <?php
    }


    /***
     * Muestra la informaci?n b?sica del formulario para solicitar un servicio
     * @param $restaurant Restaurant restaurante a mostrar, puede ser nulo
     */
    function show_basic_order_form($restaurant)
    {

        wp_enqueue_style("mecatocss", MECATO_PLUGIN_URL . 'inc/css/mecato.css');
        wp_enqueue_style("mecatocss_weekline", MECATO_PLUGIN_URL . 'inc/css/jquery.weekline.css');
        /*wp_enqueue_script("google-maps",'http://maps.googl1e.com/maps/api/js?sensor=false' );
        wp_enqueue_script("handlebars",'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.4/handlebars.min.js' );
        wp_enqueue_script("jqueryvalidate",'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js' );*/


        ?>
        <div class="col-sm-<?php echo isset($restaurant) ? "12" : "6"; ?>">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3 class="panel-title">Detalle del restaurante</h3>
                </div>

                <div class="panel-body">


                        <?php
                            if($restaurant != null)
                            {
                                ?>
                                <div  class="col-xs-12" style="text-align: center;">
                                    <a id="singlebutton" name="singlebutton" class="btn btn-xs btn-primary " role="button"
                                       href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_CREATE_MENU) ?>?restId=<?php echo $restaurant->id ?>">
                                        Agregar plato
                                    </a>
                                </div>
                                <?php
                            }
                        ?>



                    <div class="col-xs-12 addressField" data-valfor="name">
                        <div class="form-group required">
                            <label for="restaurant_name" class="control-label">Nombre</label>
                            <input id="restaurant_name" name="restaurant_name" type="text" data-var="restaurant_name"
                                   class="form-control required" maxlength="50"
                                   value="<?php echo $restaurant->name ?>"/>
                            <input id="restaurant_lat" name="restaurant_lat" type="hidden"
                                   value="<?php echo $restaurant->lat ?>"/>
                            <input id="restaurant_lon" name="restaurant_lon" type="hidden"
                                   value="<?php echo $restaurant->lon ?>"/>
                        </div>
                    </div>
                    <div class="col-xs-12 addressField" data-valfor="address">
                        <div class="form-group required">
                            <label for="restaurant_address" class="control-label">Direcci&oacute;n</label>
                            <input id="restaurant_address" name="restaurant_address" type="text"
                                   data-var="restaurant_address"
                                   class="form-control required" maxlength="50"
                                   value="<?php echo $restaurant->address ?>"/>
                        </div>
                    </div>
                    <div class="col-xs-12 addressField" data-valfor="description">
                        <div class="form-group">
                            <label for="restaurant_description" class="control-label">Descripción</label>
                            <textarea id="restaurant_description" name="restaurant_description" type="text"
                                      data-var="restaurant_description"
                                      class="form-control required"
                                      maxlength="500"><?php echo $restaurant->description ?></textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 addressField" data-valfor="phone">
                        <div class="form-group">
                            <label for="restaurant_phone" class="control-label">Telefono</label>
                            <input id="restaurant_phone" name="restaurant_phone" type="text" data-var="restaurant_phone"
                                   class="form-control required" data-val="int" maxlength="50"
                                   value="<?php echo $restaurant->phone ?>"/>
                        </div>
                    </div>

                    <?php
                    if ($restaurant != null) {
                        $this->show_edit_form_fields($restaurant);
                    }
                    ?>

                    <div class="col-sm-offset-2 col-sm-12">
                        <input type="button" id="btnNewService" class="btn btn-lg btn-success" role="button"
                                  value="<?php echo($restaurant != null ? "Actualizar restaurante" : "Guardar Restaurante") ?>"/>
                        <a href="<?php  echo ($restaurant != null ? get_permalink($restaurant->id) : get_site_url())?>" class="btn btn-lg btn-default">Cancelar</a>

                    </div>
                </div>
            </div>
        </div>
        <style>
            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }
            /*
            #dropZone {
                background: gray;
                border: black dashed 3px;
                width: 200px;
                padding: 50px;
                text-align: center;
                color: white;
            }*/
        </style>

        <?php
    }

    function show_gallery($restaurant)
    {
        ?>
        <h3 class="entry-title">Imagenes de <?php echo $restaurant->name ?></h3>
        <?php
        if(count($restaurant->images) > 0)
        {
            $commonView = new CommonView();
            $commonView->show_gallery($restaurant->images);
        }
    }

    /***
     * Muestra el resto de la información
     * @param $restaurant Restaurant Información del restaurante a actualizar
     */
    function show_edit_form_fields($restaurant)
    {
        wp_enqueue_style("mecatocss_dropzone", MECATO_PLUGIN_URL . 'inc/css/dropzone.css');


        ?>
        <style>
            .dz-details{
                display: none !important;
            }
        </style>
        
        <div class="col-xs-12 addressField" data-valfor="schedule">
            <div class="form-group">
                <label for="restaurant_schedule" class="control-label">Horario</label>
                <span class="spanSchedule"></span>
                De <?php $this->hour_selector("Open", 8) ?>
                a <?php $this->hour_selector("Close", 17) ?>
                <input id="restaurant_schedule" name="restaurant_schedule" type="hidden"
                       value="<?php echo $restaurant->schedule ?>">
            </div>
        </div>
        <?php
            $this->show_gallery($restaurant);
        ?>
        <div class="col-xs-12" id="images">
            <div class="dropzone" id="dropzoneForm">
                <div class="fallback">
                    <input name="file" type="file" multiple />
                    <input type="submit" value="Upload" />
                </div>
                <div class="dz-message">Selecciona o arrastra los archivos del restaurante</div>
            </div>
        </div>


        <?php

    }


    /***
     * Muestra un combo con la información del las horas del dia
     * @param $name String nombre del combo, ya sea horas para abrir o para cerrar
     * @param $defaultHour int Hora por defecto
     */
    function hour_selector($name, $defaultHour)
    {
        ?>
        <select id="txt<?php echo $name ?>Hour" style="width:120px" class="timeHourSchedule">
            <?php
            for ($i = 0; $i < 24; $i++) {
                $iText = $i < 10 ? "0" + $i : $i;
                $selected = $defaultHour == $i ? "selected" : "";

                ?>
                <option <?php echo $selected ?>><?php echo $iText ?>:00</option>
                <option><?php echo $iText ?>:30</option>
                <?php
            }
            ?>
        </select>
        <?php
    }

}