<?php

/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 7:07 PM
 */

require_once("Location.php");

class Car
{
    public $db_id;
    public $name;
    public $num_places;

    public static function save_to_DB($conn, $car){
        $query = "INSERT INTO `car`( `name`, `places_number`) VALUES ('$car->name', $car->num_places)";
        $res = mysqli_query($conn, $query);
        return $res ? true : false;
    }

    public static function get_by_id($id, $conn) {
        $res = mysqli_query($conn, "SELECT * FROM `car` WHERE `id` ='$id''");
        $row = mysqli_fetch_array($res);
        if ($row) {
            $car = new Car();
            $car->db_id = $row["id"];
            $car->name = $row["name"];
            $car->num_places = $row["places_number"];
            return $car;
        }
        return null;
    }
}