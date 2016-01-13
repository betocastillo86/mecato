<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/31/2015
 * Time: 1:06 PM
 */
require_once("CommonView.php");

class DetailsMenuView
{
    function __construct()
    {
        add_shortcode('mecato_menu_details', array($this, 'show_view'));
    }


    function show_view()
    {
        $post = get_post();

        $menuService = new MenuService();

        $menu = $menuService->getMenuById();
        ?>
        <div id="divMainSection">
            <div class="row">
                <div class="col-md-6">
                    <div><?php echo $post->post_content ?></div>
                    <?php
                    if(isset($menu->price))
                    {
                        ?>
                        <span class="label label-success">$<?php echo $menu->price ?></span>
                        <?php
                    }
                    $this->show_topics($menu)
                    ?>

                </div>
                <div class="col-md-6">
                    <?php $this->show_restaurant($menu->restaurantId) ?>
                </div>
            </div>



            <?php
            ;
            $this->show_gallery($menu);
            $this->show_update_menu($post, $menu->restaurantId);
            ?>

        </div>

        <?php
    }


    /****
     * Muestra la información del restaurante en el menu
     * @param $restaurantId
     */
    function show_restaurant($restaurantId)
    {
        $restaurantService = new RestaurantService();
        $restaurant = $restaurantService->getRestaurantById($restaurantId);
        ?>
        <input id="rest_lat" value="<?php echo $restaurant->lat ?>" type="hidden"/>
        <input id="rest_lon" value="<?php echo $restaurant->lon ?>" type="hidden"/>
        <div><b>Restaurante <?php echo $restaurant->name ?></b></div>
        <div id="divMap" style="width:100%; height: 70px;"></div>
        <div><?php echo $restaurant->description ?>
            <a href="<?php echo get_permalink($restaurantId) ?>" class="btn btn-xs btn-primary">Ver más</a>
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

    function show_menu($post)
    {
        $menuService = new MenuService();
        $menus = $menuService->getMenuByRestaurantId($post->ID);
        ?>
        <h2 >Platos en <?php echo $post->post_title ?></h2>
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

    /***
     * @param $menu Menu
     */
    function show_gallery($menu)
    {

        ?>
        <h2 >Imagenes de <?php echo $menu->name ?></h2>
        <?php
        if(count($menu->images) > 0)
        {

            $commonView = new CommonView();
            $commonView->show_gallery($menu->images);
        }
        else
        {
            ?>
            <div class="alert alert-danger" role="alert">
                <strong>Oops!</strong> No tenemos fotos de <?php echo $menu->name ?>. Ayudanos si tienes.
                <a href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_EDIT_MENU)."?id=".$menu->id.'&restId='.$menu->restaurantId.'#images' ?>" class="btn btn-xs btn-danger">Subir fotos</a>
            </div>
            <?php
        }
    }

    /***
     * @param $post WP_Post
     */
    function show_update_menu($post, $restaurantId)
    {
        ?>
        <div class="row">
            <div class="alert alert-warning" role="alert">
                <strong>Ayuda!</strong> Si conoces más información acerca de este plato ayudanos a completarla.
                <a href="<?php echo get_permalink(MECATO_PLUGIN_PAGE_EDIT_MENU)."?id=".$post->ID.'&restId='.$restaurantId ?>" class="btn btn-sm btn-warning">Completar información</a>
            </div>
        </div>

        <?php
    }

    /***
     * @param $menu Menu
     */
    function show_topics($menu)
    {
        ?>
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Ingredientes</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach ($menu->topics as $topic) {
                        ?>
                        <tr>
                            <td><img src="<?php echo MECATO_PLUGIN_URL.'inc/img/menuIcons/'.$topic->slug.'.png' ?>" width="20" height="20" /> <?php echo $topic->name ?></td>
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

}