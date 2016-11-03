<?php
/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 7:10 PM
 */

require_once("User.php");
require_once("Location.php");
require_once("dbconnect.php");

class Ride
{
    public $db_id;
    public $user;
    public $start_point;
    public $end_point;
    public $start_time;
    public $end_time;
    public $price;
    public $note;
    public $reserved_places;



    public static function save_to_DB($conn, $ride){
        $start_time = $ride->start_time->getTimestamp();
        $end_time = $ride->end_time->getTimestamp();
        $user_id = $ride->user->db_id;
        $start_point_id = $ride->start_point->db_id;
        $end_point_id =  $ride->end_point->db_id;
        $query = "INSERT INTO `ride`( `user_id`, `start_point_id`, `end_point_id`, `start_time`, `end_time`,`price`, `note`, `reserved_places`)
                  VALUES ($user_id,$start_point_id, $end_point_id,FROM_UNIXTIME($end_time),FROM_UNIXTIME($start_time), $ride->price, '$ride->note',$ride->reserved_places)";
        $res = mysqli_query($conn, $query);

        return $res ? true : false;
    }

    public static function get_by_id($id, $conn) {
        $res = mysqli_query($conn, "SELECT * FROM `ride` WHERE `id` ='$id'");
        $row = mysqli_fetch_array($res);
        if ($row) {
            $ride = row_to_object($row, $conn);
            return $ride;
        }
        return null;
    }

    public static function get_rides_for_user($user_id, $conn){
        $query = "SELECT * FROM `ride` WHERE `user_id` ='$user_id'";
        $res = mysqli_query($conn, $query);

        $arr = array();
        while ($row = mysqli_fetch_array($res)) {
            array_push($arr, Ride::row_to_object($row, $conn));
        }
        return $arr;
    }

    private static function row_to_object($row, $conn){
        $ride = new Ride();
        $start_point_id =  $row["start_point_id"];
        $end_point_id =  $row["end_point_id"];
        $ride->start_point = Location::get_by_id($start_point_id, $conn);
        $ride->end_point = Location::get_by_id($end_point_id, $conn);
        $ride->start_time = new DateTime();
        $ride->start_time->setTimestamp(strtotime( $row["start_time"]));
        $ride->end_time = new DateTime();
        $ride->end_time->setTimestamp(strtotime( $row["start_time"]));
        $ride->start_time->format("Y-m-d H:i:s");
        $ride->price = $row["price"];
        $ride->note = $row["note"];
        $ride->reserved_places = $row["reserved_places"];
        $ride->user = User::get_by_id($row["user_id"], $conn);
        return $ride;
    }

    public static function get_indentical_locations($lat1, $lat2, $lg1, $lg2, $start_time, $end_time, $conn){
        $sql_format = "Y-m-d H:i:s";
        $sql_time_1 = $start_time->format($sql_format);
        $sql_time_2 = $end_time->format($sql_format);

        $query = "SELECT * FROM `ride` R 
                    WHERE (R.start_point_id
                    IN (SELECT id FROM `location` WHERE (ROUND(lat, 6) = ROUND($lat1, 6) 
                      AND ROUND(lg, 6) = ROUND($lg1, 6))))
                           AND (R.end_point_id IN 
                           (SELECT id FROM `location` WHERE (ROUND(lat, 6) = ROUND($lat2, 6) AND ROUND(lg, 6) = ROUND($lg2, 6)))) 
                      AND R.start_time >='$sql_time_1' AND R.end_time <= '$sql_time_2'";
        $res = mysqli_query($conn, $query);
        $arr = array();
        print $query;
        while ($row = mysqli_fetch_array($res)) {
            array_push($arr, Ride::row_to_object($row, $conn));
        }

        return $arr;
    }

    public static  function parse_time($string){
        $time = DateTime::createFromFormat("m/d/Y h:i A", $string);

        return $time;
    }



}