<?php

/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 7:07 PM
 */


class Location
{
    public $db_id;
    public $name;
    public $lat;
    public $lg;

    public static function save_to_DB($conn, $location){
        $query = "SELECT MAX(id) AS MAXID FROM `location` ";
        $res = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($res);
        $id = $row["MAXID"];
        $id++;
        $query = "INSERT INTO `location`( `id`,`name`, `lat`, `lg`)
                  VALUES ($id, '$location->name', $location->lat, $location->lg)";
        $res = mysqli_query($conn, $query);
        return $res ? $id : null;
    }

    public static function get_by_id($id, $conn) {
        $query = "SELECT * FROM `location` WHERE `id` ='$id'";
        $res = mysqli_query($conn,$query);
        $row = mysqli_fetch_array($res);
        if ($row) {
            $location = new Location();
            $location->db_id = $row["id"];
            $location->name = $row["name"];
            $location->lat = $row["lat"];
            $location->lg = $row["lg"];
            return $location;
        }
        return null;
    }



}