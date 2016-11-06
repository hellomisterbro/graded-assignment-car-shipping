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

    }

    public static function get_by_rideid($user_id){

    }

    public static function delete_by_ride($ride_id, $conn){
        $query = "DELETE FROM `passenger` WHERE `ride_id` = $ride_id)";
        $res = mysqli_query($conn, $query);
        print $query;
        return $res ? true : false;
    }

    public static function delete_by_user_and_ride($user_id, $ride_id, $conn){
        $query = "DELETE FROM `passenger` WHERE `user_id` = $user_id AND `ride_id` = $ride_id)";
        $res = mysqli_query($conn, $query);
        return $res ? true : false;
    }

    private static function row_to_object($row){

    }

}