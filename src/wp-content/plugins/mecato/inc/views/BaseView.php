<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 1/10/2016
 * Time: 2:39 PM
 */

class BaseView
{
    private $footer;

    function __construct()
    {
        $this->show_footer();
    }

    function show_footer()
    {
        $footer = new FooterView();
    }
}