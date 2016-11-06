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
    public $reservation_places;
    public $weekly;
    public $exceptions_days;

    function add_day($string)
    {
        echo $string;
        $format = "m/d/Y";
        $datetime = date_create_from_format($format, $string);

        array_push($this->exeptions_days, $datetime);

    }

    public static function save_to_DB($conn, $ride)
    {
        $start_time = $ride->start_time->getTimestamp();
        $end_time = $ride->end_time->getTimestamp();
        $user_id = $ride->user->db_id;
        $start_point_id = $ride->start_point->db_id;
        $end_point_id = $ride->end_point->db_id;
        if ($ride->weekly != 1) {
            $ride->weekly = 0;
        }

        $query = "INSERT INTO `ride`( `user_id`, `start_point_id`, `end_point_id`, `start_time`, `end_time`,`price`, `note`, `reservation_places`, `weekly`)
                  VALUES ($user_id,$start_point_id, $end_point_id,FROM_UNIXTIME($start_time),FROM_UNIXTIME($end_time), $ride->price, '$ride->note',$ride->reservation_places, $ride->weekly)";
        $res = mysqli_query($conn, $query);
        print $query;
        if ($ride->exeptions_days) {
            foreach ($ride->exeptions_days as $exeptions_day) {
                $mysqldate = date('Y-m-d', $exeptions_day->getTimestamp());
                $query = "SELECT * FROM `ride` WHERE user_id =$user_id  AND start_point_id = $start_point_id AND end_point_id = $end_point_id  
                        AND start_time = FROM_UNIXTIME($start_time)AND end_time = FROM_UNIXTIME($end_time) AND price = $ride->price AND note = '$ride->note'";
                print $query;
                $res = mysqli_query($conn, $query);
                $row = mysqli_fetch_array($res);
                $ride_id = $row["id"];
                $query = "INSERT INTO `exception_dates`(  `ride_id`, `date`) VALUES ($ride_id,'$mysqldate')";
                $res = mysqli_query($conn, $query);
            }
        }
        print $query;
        return $res ? true : false;
    }

    public static function get_by_id($id, $conn)
    {
        $res = mysqli_query($conn, "SELECT * FROM `ride` WHERE `id` ='$id'");
        $row = mysqli_fetch_array($res);
        if ($row) {
            $ride = Ride::row_to_object($row, $conn);
            return $ride;
        }
        return null;
    }

    public static function get_rides_for_user($user_id, $conn)
    {
        $query = "SELECT * FROM `ride` WHERE `user_id` ='$user_id'";
        $res = mysqli_query($conn, $query);

        $arr = array();
        while ($row = mysqli_fetch_array($res)) {
            array_push($arr, Ride::row_to_object($row, $conn));
        }
        return $arr;
    }

    private static function row_to_object($row, $conn)
    {
        $ride = new Ride();
        $start_point_id = $row["start_point_id"];
        $end_point_id = $row["end_point_id"];
        $ride->db_id = $row["id"];
        $ride->start_point = Location::get_by_id($start_point_id, $conn);
        $ride->end_point = Location::get_by_id($end_point_id, $conn);
        $ride->start_time = new DateTime();
        $ride->start_time->setTimestamp(strtotime($row["start_time"]));
        $ride->end_time = new DateTime();
        $ride->end_time->setTimestamp(strtotime($row["start_time"]));
        $ride->start_time->format("Y-m-d H:i:s");
        $ride->price = $row["price"];
        $ride->note = $row["note"];
        $ride->weekly = boolval($row["weekly"]);
        $ride->reservation_places = $row["reservation_places"];
        $ride->user = User::get_by_id($row["user_id"], $conn);
        return $ride;
    }

    public static function get_indentical_locations($lat1, $lat2, $lg1, $lg2, $start_time, $end_time, $conn)
    {
        $sql_format = "Y-m-d H:i:s";
        $sql_time_1 = $start_time->format($sql_format);
        $sql_time_2 = $end_time->format($sql_format);

        $query = "SELECT * FROM `ride` R 
                    WHERE (R.start_point_id
                    IN (SELECT id FROM `location` WHERE (ROUND(lat, 6) = ROUND($lat1, 6) 
                      AND ROUND(lg, 6) = ROUND($lg1, 6))))
                           AND (R.end_point_id IN 
                           (SELECT id FROM `location` WHERE (ROUND(lat, 6) = ROUND($lat2, 6) AND ROUND(lg, 6) = ROUND($lg2, 6)))) 
                      AND R.start_time >='$sql_time_1' AND R.start_time <= '$sql_time_2'";

        $res = mysqli_query($conn, $query);
        $arr = array();
        print $query;
        while ($row = mysqli_fetch_array($res)) {
            array_push($arr, Ride::row_to_object($row, $conn));
        }

        return $arr;
    }

    public static function join_ride($user_id, $ride_id, $conn)
    {
        $query = "INSERT INTO `passenger`(`user_id`, `ride_id`) VALUES ($user_id, $ride_id)";
        $res = mysqli_query($conn, $query);
        if ($res) {
            $ride = Ride::get_by_id($ride_id, $conn);
            $places = $ride->reservation_places - 1;
            $query = "UPDATE `ride` SET `reservation_places`=$places WHERE id = $ride_id";
            $res = mysqli_query($conn, $query);
        }
    }


    public static function parse_time($string)
    {
        $time = DateTime::createFromFormat("m/d/Y h:i A", $string);
        return $time;
    }


}