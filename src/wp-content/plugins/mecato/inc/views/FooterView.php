<?php
/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 1/10/2016
 * Time: 12:05 PM
 */
class FooterView
{
    function __construct()
    {
        add_action('wp_footer', array($this, 'show_view') );
    }

    function show_view()
    {
        ?>

            <div id="mainModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">{{title}}</h4>
                        </div>
                        <div class="modal-body">
                            <p>{{message}}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        <?php

    }
}