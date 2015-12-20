<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/19/2015
 * Time: 11:44 AM
 */
class EditRestaurantView
{
    function __construct()
    {
        add_shortcode('mecato_edit_restaurant', array($this, 'show_view'));
    }

    function show_view($attr)
    {


        ?>
        <div id="divMainSection">
            <form >
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

        wp_enqueue_style("mecatocss",MECATO_PLUGIN_URL.'css/mecato.css' );
        /*wp_enqueue_script("google-maps",'http://maps.google.com/maps/api/js?sensor=false' );
        wp_enqueue_script("handlebars",'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.4/handlebars.min.js' );
        wp_enqueue_script("jqueryvalidate",'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js' );*/




        ?>

        <div class="col-xs-12 addressField">
            <div class="form-group">
                <label for="address_from">Direcci&oacute;n origen</label>
                <input id="address_from" name="address_from" type="text" data-var="address_from" class="form-control required"  maxlength="50" />
                <input id="address_from_lat" type="hidden" />
                <input id="address_from_lon" type="hidden" />
            </div>
        </div>
        <div class="col-xs-12 addressField">
            <div class="form-group">
                <label for="address_to">Direcci&oacute;n destino</label>

                <input id="address_to" name="address_to" type="text" data-var="address_to" class="form-control required"   maxlength="50" />
                <input id="address_to_lat" type="hidden" />
                <input id="address_to_lon" type="hidden" />
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="order_day">D&iacute;a</label>
                <input id="order_day" type="text" name="order_day" data-var="order_day" class="form-control required" value="<?php echo current_time('Y/m/d') ?>"   />
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="order_time">Hora</label>
                <input id="order_time" type="text" name="order_time" data-var="order_time" class="form-control required" data-minute-step="10" value="<?php echo current_time('timestamp') ?>"  />
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label for="order_description">Descripci&oacute;n</label>
                <textarea id="order_description" data-var="order_description" name="order_description" class="form-control required" placeholder="Describe como mejor te parezca tu envio"></textarea>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label for="order_value">Valor del objeto a env&iacute;ar</label>
                <input id="order_value" type="text" name="order_value" data-var="order_time" class="form-control required" for-type="number" maxlength="20"   />
            </div>
        </div>
        <div class="col-sm-offset-2 col-sm-12">
            <p><input type="button" id="btnNewService" class="btn btn-lg btn-success"  role="button" value="Solicitar Servicio"/></p>
        </div>
        <?php
    }

}