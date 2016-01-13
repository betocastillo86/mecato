<?php
/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 1/3/2016
 * Time: 11:35 AM
 */
class SearchRestaurantView extends BaseView
{
    function __construct()
    {
        parent::__construct();
        add_shortcode('mecato_search', array($this, 'show_view'));
    }

    function show_view()
    {
        $this->show_styles();
        ?>
        <div id="divFullLoading"></div>
        <div id="divMainSection">
            <?php
            $this->show_filter();
            $this->show_filter_list();
            $this->show_map();
            ?>
        </div>
        <?php

    }

    function show_map()
    {
        ?>
        <div id="divMap" style="width:100%; height:500px"></div>


        <script id="templateMapInfoRest" type="text/x-handlebars-template" >
            <div  style="padding: 3px;text-align: center;">
                <img src="{{#if thumbnail}} {{thumbnail}} {{else}} /wp-content/plugins/mecato/inc/img/icons/noimage.png {{/if}}" class="img-thumbnail" style="width:60px" />
            </div>
            <div >
                <h4>{{name}}</h4>
                <p>
                    {{address}} <br>
                    {{phone}}
                </p>
                <a class="btn btn-xs btn-default" href="{{guid}}">Ver m�s</a>
            </div>
        </script>

        <?php
    }

    function show_filter_list()
    {
        ?>
        <div>
            <div id="divFilteredList" class="list-group">


            </div>
            <script id="templateFilteredList" type="text/x-handlebars-template">
                {{#if list.length}}
                {{#each list}}
                <div id="officeList{{id}}" data-id="{{id}}" class="office-box list-group-item">
                    <div class="col-sm-4" style="padding: 3px;">
                        <img src="{{#if thumbnail}} {{thumbnail}} {{else}} /wp-content/plugins/mecato/inc/img/icons/noimage.png {{/if}}" class="img-thumbnail" width="100%" height="40" />
                    </div>
                    <div class="col-sm-8" style="padding: 5px;">
                        <h5 class="list-group-item-heading"><a href="{{guid}}">{{name}}</a> </h5>
                        <p class="list-group-item-text">{{address}}</p>
                        <input class="btn btn-xs btn-danger" type="button" value="Ver en mapa" />
                        <a class="btn btn-xs btn-default" href="{{guid}}">Ver</a>
                    </div>
                    <div class="clearfix"></div>

                    <?php /*
                    <div>
                        <img src="{{thumbnail}}" class="img-thumbnail" width="100%" height="40" />
                    </div>
                    <div>
                        <a href="{{guid}}"><b>{{name}}</b></a>
                    </div>
                    <div>

                    </div>
                    <div><a href="{{guid}}" class="btn">Visitalo</a></div>*/?>
                </div>
                {{/each}}

                {{else}}
                <div class="list-group-item">
                    <h4 class="list-group-item-heading">No se encontraron resultados</h4>

                    {{#if total}}
                        <p class="list-group-item-text">
                            En el area que estas viendo no hay restaurantes. Pero si quieres puedes ir al m�s cercano.
                        </p>
                        <input type="button" id="btnShowNearest" class="btn btn-xs btn-default" value="Ver m�s cercano" />
                    {{else}}
                        <p class="list-group-item-text">Intenta realizando una busqueda diferente, depronto encontrar�s lo que buscas</p>
                    {{/if}}


                </div>
                {{/if}}


            </script>
        </div>
        <?php
    }

    function show_filter()
    {
        ?>
        <div id="divFilter">

            <div id="divVendorResult"></div>

            <div class="inputs">
                <select id="ddlCity" name="restaurant_city" style="display:block">
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
            </div>
            <div>
                <div id="divSelectedFilter"></div>
                <input id="txtFilter" type="text" placeholder="Busca restaurantes por nombre" />
            </div>
            <div class="btn-group" data-toggle="buttons">
                <a type="button" class="btn btn-default" data-radio-name="menu_type" data-id="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGETARIAN?>">
                    Vegetariano
                </a>
                <a type="button" class="btn btn-default" data-radio-name="menu_type" data-id="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGAN?>">Vegano
                </a>

                <script>
                    window.menuTypes = {
                        vegetarian : '<?php echo MECATO_PLUGIN_PAGE_TAX_VEGETARIAN?>',
                        vegan : '<?php echo MECATO_PLUGIN_PAGE_TAX_VEGAN ?>'
                    }
                </script>
            </div>

            <div>
                <input type="button" value="Buscar" class="btn" />
            </div>


            <script id="templateRestCount" type="text/x-handlebars-template">
                {{#if this}}
                Se encontraron {{this}} resultados
                {{else}}
                <span style="color:red">No se encontraron resultados</span>
                {{/if}}
            </script>

            <script id="templateSelectedItem" type="text/x-handlebars-template">
                <ul class="tagit ui-widget" style="width:90%" data-type="{{type}}">
                    <li class="tagit-choice">
                        <span class="tagit-label">{{label}}</span>
                        <a class="tagit-close"><span class="icon-close"></span></a>
                    </li>
                </ul>
            </script>


        </div>
        <?php
    }


    function show_styles()
    {
        wp_enqueue_style("mecatocss", MECATO_PLUGIN_URL . 'inc/css/mecato.css');
        ?>
        <style>
            #divFilter {
                width: 350px;
                position: absolute;
                left: 10px;
                z-index: 3;
                background: #fff;
            }
            #content
            {
                margin: 0px !important;
                padding: 0px !important;
                width:100%;
            }

            .main-content-area
            {
                margin: 0px !important;
                padding: 0px !important;
                width:100%;
            }
            .entry-header
            {
                display: none;
            }

            #divFilteredList {
                right: 0;
                width: 250px;
                background: #fff;
                position: absolute !important;
                z-index: 3;
            }

            .list-group-item.active, .list-group-item.active:hover
            {
                background-color:#f2f2f2;
                border-color:gray;
            }

        </style>
        <?php
    }
}