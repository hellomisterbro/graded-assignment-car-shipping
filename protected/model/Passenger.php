<?php

/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 11/5/16
 * Time: 9:36 PM
 */
class Passenger
{
    public $id;
    public $ride;
    public $user;

    public static function save($passenger, $conn){

        $query = "INSERT INTO `passenger`(`user_id`, `ride_id`) VALUES ($passenger->user->db_id,$passenger->ride->db_id)";
        $res = mysqli_query($conn, $query);
        return $res ? true : false;
    }

    public static function get_by_userid($user_id, $conn){
        $query = "SELECT * FROM `passenger` WHERE `user_id` = $user_id";
        $res = mysqli_query($conn, $query);
        return $res ? $res["ride_id"] : false;
    }

    public static function get_by_rideid($user_id){

    }

    private static function row_to_object($row){

    }

}