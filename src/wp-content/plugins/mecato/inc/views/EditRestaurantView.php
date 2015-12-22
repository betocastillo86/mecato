<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/19/2015
 * Time: 11:44 AM
 */
require_once(MECATO_PLUGIN_DIR.'inc/services/RestaurantService.php');
class EditRestaurantView
{
    function __construct()
    {
        add_shortcode('mecato_edit_restaurant', array($this, 'show_view'));
    }

    function show_view($attr)
    {
        if(isset($_POST['restaurant_name']))
        {
            $this->save_restaurant();
        }

        ?>
        <div id="divMainSection">
            <form action="" method="post" >
                <div class="row">
                    <div class="col-sm-6">
                        <div class="panel panel-default">

                            <div class="panel-heading">
                                <h3 class="panel-title">Ubicaci&oacute;n</h3>
                            </div>

                            <div class="panel-body">
                                <?php $this->show_map() ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="panel panel-default">

                            <div class="panel-heading">
                                <h3 class="panel-title">Detalle del restaurante</h3>
                            </div>

                            <div class="panel-body">
                                <?php $this->basic_order_form() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="divModalNewOrder"></div>
            </form>
        </div>

    <?php

    }

    function save_restaurant()
    {
        if(isset($_POST['restaurant_name']) && isset($_POST['restaurant_address'])
            && isset($_POST['restaurant_lat']) && isset($_POST['restaurant_lon']))
        {
            $model = new Restaurant();
            $model->name = $_POST['restaurant_name'];
            $model->address = $_POST['restaurant_address'];
            $model->schedule = $_POST['restaurant_schedule'];
            $model->lat = $_POST['restaurant_lat'];
            $model->lon = $_POST['restaurant_lon'];
            $model->userId = get_current_user_id();

            if(isset($_POST['restaurant_phone']))
                $model->phone = $_POST['restaurant_phone'];

            //Guarda el restaurante
            $restaurantService = new RestaurantService();
            $restaurantService->insertRestaurant($model);
        }
        else
        {
            ?>
                <h2>Los datos son invalidos</h2>
            <?php
        }
    }
    /***
     * Muestra el mapa con las funcionalidades
     */
    function show_map()
    {
        ?>


        <style>
            body { background-color:#CCC }
            #map_location {  height: 440px;
                padding: 20px;
                border: 2px solid #CCC;
                margin-bottom: 20px;
                background-color:#FFF }
            #map_location { height: 400px }
            @media all and (max-width: 991px) {
                #map_location  { height: 650px }
            }
        </style>

        <div id="map_location"></div>

        <?php
    }


    /***
     * Muestra la informaci?n b?sica del formulario para solicitar un servicio
     */
    function basic_order_form()
    {

        wp_enqueue_style("mecatocss",MECATO_PLUGIN_URL.'inc/css/mecato.css' );
        wp_enqueue_style("mecatocss_weekline",MECATO_PLUGIN_URL.'inc/css/jquery.weekline.css' );
        /*wp_enqueue_script("google-maps",'http://maps.googl1e.com/maps/api/js?sensor=false' );
        wp_enqueue_script("handlebars",'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.4/handlebars.min.js' );
        wp_enqueue_script("jqueryvalidate",'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js' );*/




        ?>

        <div class="col-xs-12 addressField" data-valfor="name">
            <div class="form-group required">
                <label for="restaurant_name" class="control-label">Nombre</label>
                <input id="restaurant_name" name="restaurant_name" type="text" data-var="restaurant_name" class="form-control required"  maxlength="50" />
                <input id="restaurant_lat" name="restaurant_lat" type="hidden" />
                <input id="restaurant_lon" name="restaurant_lon" type="hidden" />
            </div>
        </div>
        <div class="col-xs-12 addressField" data-valfor="address">
            <div class="form-group required">
                <label for="restaurant_address"  class="control-label">Direcci&oacute;n</label>
                <input id="restaurant_address" name="restaurant_address" type="text" data-var="restaurant_address" class="form-control required"   maxlength="50" />
            </div>
        </div>
        <div class="col-xs-12 addressField" data-valfor="phone">
            <div class="form-group">
                <label for="restaurant_phone"  class="control-label">Telefono</label>
                <input id="restaurant_phone" name="restaurant_phone" type="text" data-var="restaurant_phone" class="form-control required" data-val="int"   maxlength="50" />
            </div>
        </div>
        <div class="col-xs-12 addressField"  data-valfor="schedule">
            <div class="form-group">
                <label for="restaurant_schedule"  class="control-label">Horario</label>
                <span class="spanSchedule"></span>
                De <?php $this->hour_selector("Open", 8) ?>
                a <?php $this->hour_selector("Close", 17) ?>
                <input id="restaurant_schedule" name="restaurant_schedule" type="hidden" >
            </div>
        </div>


        <div class="col-sm-offset-2 col-sm-12">
            <p><input type="button" id="btnNewService" class="btn btn-lg btn-success"  role="button" value="Guardar Restaurante"/></p>

        </div>
        <style>
            .form-group.required .control-label:after {
                content:"*";
                color:red;
            }
        </style>
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
            for ($i = 0; $i < 24; $i++)
            {
                $iText = $i < 10 ? "0" + $i : $i;
                $selected = $defaultHour == $i ?"selected" : "";

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