<?php

/**
 * Created by PhpStorm.
 * User: Beto
 * Date: 12/21/2015
 * Time: 10:56 PM
 */
class Restaurant
{
    public $id;
    public $name;
    public $address;
    public $city;
    public $lat;
    public $lon;
    public $phone;
    public $schedule;
    public $userId;
    public $description;
    public $images;
    public $guid;
    public $thumbnail;
    /***
     * @var int numero de platos veganos
     */
    public $numVegan;
    /***
     * @var int numero de platos vegetarianos
     */
    public $numVegetarian;
}