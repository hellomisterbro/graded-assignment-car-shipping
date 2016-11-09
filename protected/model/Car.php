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
        $query = "INSERT INTO `car`( `name`, `place_number`) VALUES ('$car->name', $car->num_places)";
        print $query;
        $res = mysqli_query($conn, $query);
        $query = "SELECT LAST_INSERT_ID();";
        $res = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($res);
        return $res ? intval($row['LAST_INSERT_ID()']) : false;
    }

    public static function get_by_id($id, $conn) {
        $res = mysqli_query($conn, "SELECT * FROM `car` WHERE `id` =$id");
        if(!$res){
            return null;
        }
        $row = mysqli_fetch_array($res);
        if ($row) {
            $car = new Car();
            $car->db_id = $row["id"];
            $car->name = $row["name"];
            $car->num_places = intval($row["place_number"]);
            return $car;
        }
        return null;
    }
}