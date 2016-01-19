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

    private $showMenuHelp = false;

    function show_view()
    {
        $this->show_styles();

        ?>
        <div id="divFullLoading"></div>
        <div id="divMainSection">
            <?php
            $this->show_help();
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


        <script id="templateMapInfoRest" type="text/x-handlebars-template">
            <div style="padding: 3px;text-align: center;">
                <img
                    src="{{#if thumbnail}} {{thumbnail}} {{else}} /wp-content/plugins/mecato/inc/img/icons/noimage.png {{/if}}"
                    class="img-thumbnail" style="width:60px"/>
            </div>
            <div>
                <h4>{{name}}</h4>

                <p>
                    {{address}} <br>
                    {{phone}}
                </p>

                <?php
                if($this->showMenuHelp)
                {
                    ?>
                    <a class="btn btn-xs btn-default" href="/crear-plato/?restId={{id}}">Crear Plato</a>
                    <?php
                }
                else
                {
                    ?>
                    <a class="btn btn-xs btn-default" href="{{guid}}">Ver más</a>
                    <?php
                }
                ?>


            </div>
        </script>

        <?php
    }


    /****
     * Muestra las variables de ayuda si son necesarias
     */
    function show_help()
    {
        //Muestra la ayuda que tiene que ver con buscar el restaurante para publicar el plato
        if(isset($_REQUEST['help_menu']))
        {
            $this->showMenuHelp = true;
            $this->show_menu_help();
        }
        else
        {
            $this->show_search_help();
        }
    }

    function show_menu_help()
    {
        ?>
        <input id="hidShowMenuHelp" type="hidden" value="1" />
        <script id="templateMenuHelp" type="text/x-handlebars-template">
            <div class="row">
                <div class="col-sm-12">
                    Para crear un plato debes seleccionar el restaurante al que pertenece. Los pasos para esto son:
                    <div>
                        <h4>1. Busca por nombre y ciudad el restaurante</h4>
                        <div class="thumbnail">
                            <img src="http://bicis.local.com/wp-content/uploads/2016/01/help1.png">
                        </div>
                        <h4>2. Seleccionalo en el mapa</h4>
                        <div class="thumbnail">
                            <img src="http://bicis.local.com/wp-content/uploads/2016/01/help2-seleccionar-restaurante.png">
                        </div>
                        <h4>3. Selecciona la opción "Crear Plato"</h4>
                    </div>
                </div>
            </div>
        </script>
        <?php
    }

    function show_search_help()
    {
        ?>
        <input id="hidShowSearchHelp" type="hidden" value="1" />
        <script id="templateSearchHelp" type="text/x-handlebars-template">
            <div class="row">
                <div class="col-sm-12">
                    Para encontrar el restaurante más cercano solo debes
                    <div>
                        <b>1. Busca por las características que más se acomoden a tus gustos o ubicación</b>
                        <div class="thumbnail">
                            <img src="http://bicis.local.com/wp-content/uploads/2016/01/help1.png">
                        </div>
                        <b>2. Selecciona en el mapa el restaurante que más te gusta</b>
                        <div class="thumbnail">
                            <img src="http://bicis.local.com/wp-content/uploads/2016/01/help2-seleccionar-restaurante.png">
                        </div>
                        <b>3. Conoce toda su información y visitalo :)</b>
                    </div>
                </div>
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
                <div class="show-filter"><span id="closeFilterList" style="width: 100%" class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></div>
                {{#if list.length}}
                {{#each list}}
                <div id="officeList{{id}}" data-id="{{id}}" class="office-box list-group-item">
                    <div class="col-sm-4" style="padding: 3px;">
                        <img
                            src="{{#if thumbnail}} {{thumbnail}} {{else}} /wp-content/plugins/mecato/inc/img/icons/noimage.png {{/if}}"
                            class="img-thumbnail" width="150" height="150"/>
                    </div>
                    <div class="col-sm-8" style="padding: 5px;">
                        <h5 class="list-group-item-heading"><a href="{{guid}}">{{name}}</a></h5>

                        <p class="list-group-item-text">{{address}}</p>
                        <input class="btn btn-xs btn-danger" type="button" value="Ver en mapa"/>

                        <?php
                        if($this->showMenuHelp)
                        {
                        ?>
                            <a class="btn btn-xs btn-default" href="/crear-plato/?restId={{id}}">Crear Plato</a>
                        <?php
                        }
                        else
                        {
                        ?>
                            <a class="btn btn-xs btn-default" href="{{guid}}">Ver</a>
                        <?php
                        }

                        ?>
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
                    <div><a href="{{guid}}" class="btn">Visitalo</a></div>*/
                    ?>
                </div>
                {{/each}}

                {{else}}
                <div class="list-group-item">
                    <h4 class="list-group-item-heading">No se encontraron resultados</h4>

                    {{#if total}}
                    <p class="list-group-item-text">
                        En el area que estas viendo no hay restaurantes. Pero si quieres puedes ir al más cercano.
                    </p>
                    <input type="button" id="btnShowNearest" class="btn btn-xs btn-default" value="Ver más cercano"/>
                    {{else}}
                    <p class="list-group-item-text">Intenta realizando una busqueda diferente, depronto encontrarás lo
                        que buscas</p>
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

            <div id="divVendorResult" class="alert" style="text-align:center; margin-bottom: 0px;"></div>
            <div class="show-filter">
                <button id="btnShowFilter" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtrar</button>
                <button id="btnShowFilterList" type="button" class="btn btn-default"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resultados</button>
            </div>

            <div id="divFilterOptions">
                <div class="show-filter"><span id="closeFilter" style="width: 100%" class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></div>
                <div>
                    <b>Filtra como desees:</b>
                </div>
                <div class="form-group">
                    <select id="ddlCity" name="restaurant_city" style="display:block" class="form-control">
                        <?php
                        $cities = get_terms('ciudad', array('hide_empty' => false));

                        foreach ($cities as $city) {
                            ?>
                            <option value="<?php echo $city->slug ?>">
                                <?php echo $city->name ?>
                            </option>
                            <?php
                        }

                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <div id="divSelectedFilter"></div>
                    <input id="txtFilter" type="text" placeholder="Busca restaurantes por nombre" class="form-control"/>
                </div>
                <div class="form-group">
                    <div class="btn-group" data-toggle="buttons">
                        <a type="button" class="btn btn-default" data-radio-name="menu_type"
                           data-id="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGETARIAN ?>">
                            Vegetariano
                        </a>
                        <a type="button" class="btn btn-default" data-radio-name="menu_type"
                           data-id="<?php echo MECATO_PLUGIN_PAGE_TAX_VEGAN ?>">Vegano
                        </a>

                        <script>
                            window.menuTypes = {
                                vegetarian: '<?php echo MECATO_PLUGIN_PAGE_TAX_VEGETARIAN?>',
                                vegan: '<?php echo MECATO_PLUGIN_PAGE_TAX_VEGAN ?>'
                            }
                        </script>
                    </div>
                </div>

                <?php /*
                <div>
                    <input type="button" value="Buscar" class="btn"/>
                </div>*/ ?>
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

            @media(max-width:767px){
                .show-filter 
                {
                    display: block !important;
                    padding: 5px;
                    text-align: center;
                }

                #divFilterOptions
                {
                    display:none;
                    position:relative !important;
                    width:100% !important;
                }

                #divFilteredList
                {
                    display:none;
                    position:relative !important;
                    width:100% !important;
                }
            }

            .show-filter
            {
                display: none;
            }
            
            #divFilterOptions {
                width: 350px;
                position: absolute;
                left: 10px;
                z-index: 3;
                background: #fff;
                padding:10px;
            }

            #content {
                margin: 0px !important;
                padding: 0px !important;
                width: 100%;
            }

            .main-content-area {
                margin: 0px !important;
                padding: 0px !important;
                width: 100%;
            }

            .entry-header {
                display: none;
            }

            #divFilteredList {
                right: 0;
                width: 250px;
                background: #fff;
                position: absolute !important;
                z-index: 3;
            }

            .list-group-item.active, .list-group-item.active:hover {
                background-color: #f2f2f2;
                border-color: gray;
            }
            .page-content, .entry-content, .entry-summary
            {
                margin-top:0px;
            }

            .row.full-width
            {
                margin-right: 0px;
                margin-left: 0px;
            }

            #primary.col-sm-12{
                padding-right: 0px;
                padding-left: 0px;
            }

        </style>
        <?php
    }
}