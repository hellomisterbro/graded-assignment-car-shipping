<?php

/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 7:07 PM
 */

require_once("Ride.php");
require_once("Car.php");

class User
{
    public $db_id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $country;
    public $desc;
    public $car;
    public $is_enabled;
    public $is_admin;

    public static function save_to_DB($conn, $user){
        $password = hash('sha256', $user->password);
        $query = "INSERT INTO `user`( `name`, `email`, `password`, `phone`,
                                    `country`,`desc`, `is_enabled`, `is_admin`)
                  VALUES ('$user->name','$user->desc','$user->email','$password', '$user->phone',
                   '$user->country', 0, 0)";

        $res = mysqli_query($conn, $query);
        return $res ? true : false;
    }

    public static function get_by_id($id, $conn) {
        $query =  "SELECT * FROM `user` WHERE `id` ='$id'";
        $res = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($res);
        if ($row) {
            $user = new User();
            $user->db_id = $row["id"];
            $user->name = $row["name"];
            $user->email = $row["email"];
            $user->password = $row["password"];
            $user->phone = $row["phone"];
            $user->country = $row["country"];
            $user->desc = $row["desc"];
            $car_id = $row["car"];
//            $user->car = Car::get_by_id($car_id, $conn);
            $user->is_enabled = $row["is_enabled"];
            $user->is_admin= $row["is_admin"];
            return $user;
        }
        return null;
    }

    public static function authorization($email, $pass, $conn){
        $pass = hash('sha256', $pass);
        $res = mysqli_query($conn, "SELECT * FROM `user` WHERE `email` ='$email'");
        $row = mysqli_fetch_array($res);
        if ($pass == $row["password"]) {
            return $row["id"];
        }
        return null;
    }

}