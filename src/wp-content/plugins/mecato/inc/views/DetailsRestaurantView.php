<?php
/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/27/2015
 * Time: 11:48 AM
 */
require_once("CommonView.php");

class DetailsRestaurantView
{
    function __construct()
    {
        add_shortcode('mecato_restaurant_details', array($this, 'show_view'));
    }


    function show_view()
    {
        $post = get_post();

        $restaurantService = new RestaurantService();

        $restaurant = $restaurantService->getRestaurantById();
        ?>
        <div id="divMainSection">
            <div><?php echo $post->post_content ?></div>
            <input id="rest_lat" type="hidden" value="<?php echo $restaurant->lat ?>"/>
            <input id="rest_lon" type="hidden" value="<?php echo $restaurant->lon ?>"/>








            <?php

            $this->show_location($restaurant);
            $this->show_gallery($restaurant);
            $this->show_menu($post);
            $this->show_update_restaurant($post);
            ?>

        </div>

        <?php
    }


    function show_menu($post)
    {
        $menuService = new MenuService();
        $menus = $menuService->getMenuByRestaurantId($post->ID);
        ?>
        <h3 class="entry-title">Platos en <?php echo $post->post_title ?></h3>
        <?php
        if (count($menus) > 0) {
            ?>


            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th >Ingredientes</th>
                            <th >Precio</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach ($menus as $menu) {

                            $menuType = $menu->menuType;
                            $topics = "";
                            foreach($menu->topics as $topic)
                            {
                                if(strlen($topics)> 0 )
                                    $topics .= ", ";
                                $topics .= $topic->name;
                            }

                            ?>
                            <tr>
                                <td><?php echo $menu->name ?></td>
                                <td><?php echo $menuType->name ?><img alt="Plato <?php echo $menuType->name ?>" title="Plato <?php echo $menuType->name ?>" src="<?php echo MECATO_PLUGIN_URL.'inc/img/menuIcons/'.$menuType->slug.'.png' ?>" width="20" height="20"/></td>
                                <td><?php echo $topics ?></td>
                                <td><?php echo $menu->price ?></td>
                                <td><a href="<?php echo $menu->guid ?>" class="btn btn-xs btn-primary">Ver</a></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
        else
        {
            ?>
            <div class="alert alert-danger" role="alert">
                <strong>Oops!</strong> No tenemos platos registrados para este restaurante. Si conoces alguno ayudanos acá.
                <a href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_CREATE_MENU)."?restId=".$post->ID ?>" class="btn btn-xs btn-danger">Crear plato</a>
            </div>
            <?php
        }


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
        else
        {
            ?>
            <div class="alert alert-danger" role="alert">
                <strong>Oops!</strong> No tenemos fotos de <?php echo $restaurant->name ?>. Ayudanos si tienes.
                <a href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_EDIT_REST)."?id=".$restaurant->id.'#images' ?>" class="btn btn-xs btn-danger">Subir fotos</a>
            </div>
            <?php
        }
    }

    function show_update_restaurant($post)
    {
        ?>
        <div class="row">
            <div class="alert alert-warning" role="alert">
                <strong>Ayuda!</strong> Si conoces más información acerca de este restaurante ayudanos a completar la información.
                <a href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_EDIT_REST)."?id=".$post->ID ?>" class="btn btn-sm btn-warning">Completar información</a>
            </div>
        </div>

        <?php
    }

    /***
     * @param $restaurant Restaurant
     */
    function show_location($restaurant)
    {
        ?>
        <div class="row">
            <h3 class="entry-title">¿Dónde está <?php echo $restaurant->name ?>?</h3>

            <div id="divMap" style="width:100%; height: 150px;"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><img src="<?php echo MECATO_PLUGIN_URL.'inc/img/icons/address.png' ?>" width="20" height="20" /><?php echo $restaurant->city->name.' - '. $restaurant->address ?></td>
                        <td><img src="<?php echo MECATO_PLUGIN_URL.'inc/img/icons/phone.png' ?>" width="20" height="20" /><?php echo $restaurant->phone ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <?php

    }
}