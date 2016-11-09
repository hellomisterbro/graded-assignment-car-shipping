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
    public $photo_path;
    public $car;
    public $is_enabled;
    public $is_admin;

    public static function save_to_DB($conn, $user){
        $password = hash('sha256', $user->password);
        $query = "INSERT INTO `user`( `name`, `email`, `password`, `phone`,
                                    `country`,`desc`, `is_enabled`, `is_admin`)
                  VALUES ('$user->name','$user->email','$password', '$user->phone',
                   '$user->country','$user->desc', 0, 0)";

        $res = mysqli_query($conn, $query);
        return $res ? true : false;
    }

    public static function update_DB($user, $conn){
        $db_user = User::get_by_id($user->db_id, $conn);

        $car_id = $user->car->name =='' ? $db_user->car->db_id: (Car::save_to_DB($conn, $user->car));
        if(!$car_id){
            $car_id = 'NULL';
        }
        if($user->photo_path!=''){
            unlink($db_user->photo_path);
        } else {
            $user->photo_path = $db_user->photo_path;
        }
        $desc =  $conn->escape_string($user->desc);
        $password = $user->password == ''?$db_user->password : hash('sha256', $user->password);;
        if ($db_user){
            $query = "UPDATE `user`
                              SET `name` = '$user->name', `password` = '$password',`path_photo` = '$user->photo_path', `car_id` = $car_id,  `email` = '$user->email', `phone` = '$user->phone',
                                    `country` = '$user->country', `desc` = '$desc', `is_enabled` = 0
                              WHERE `id` = $user->db_id";
            $res = mysqli_query($conn, $query);
            if($res){
                return true;
            } else{
                return false;
            }
        }
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
            $user->photo_path = $row["path_photo"];
            $user->car = Car::get_by_id($row["car_id"], $conn);
            $user->is_enabled = boolval($row["is_enabled"]);
            $user->is_admin= boolval($row["is_admin"]);
            return $user;
        }
        return null;
    }

    public static function get_all_users($conn){
        $query = "SELECT * FROM `user` WHERE `is_admin` <> 1";
        $res = mysqli_query($conn, $query);
        $arr = array();
        while ($row = mysqli_fetch_array($res)) {
            $user = User::get_by_id($row["id"], $conn);
            array_push($arr, $user);
        }
        return $arr;
    }

    public static function authorization($email, $pass, $conn){
        $pass = hash('sha256', $pass);
        $res = mysqli_query($conn, "SELECT * FROM `user` WHERE `email` ='$email'");
        $row = mysqli_fetch_array($res);
        if ($pass == $row["password"]) {
            if(boolval($row["is_enabled"])){
                return $row["id"];
            } else {
                return false;
            }
        }
        return null;
    }

    public static function delete_user($user_id, $conn){
        $res2 = true; $res3 = true;
        $query = "DELETE FROM `ride` WHERE `user_id` = $user_id";
        $res1 = mysqli_query($conn, $query);
        if($res1) {
            $query = "SELECT * FROM `passenger` WHERE `user_id` = $user_id";
            $res2 = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_array($res2)) {
                Ride::unjoin($user_id, $row["ride_id"], $conn);
            }
            $query = "DELETE FROM `passenger` WHERE `user_id` = $user_id";
            $res3 = mysqli_query($conn, $query);
        }
        $query = "DELETE FROM `user` WHERE `id` = $user_id";
        $res4 = mysqli_query($conn, $query);
        return $res1&&$res2&&$res3 ? true : false;
    }

    public static function block_user($user_id, $conn){
        $query = "UPDATE `user` SET is_enabled = 0 WHERE `id` = $user_id";
        $res = mysqli_query($conn, $query);
    }

    public static function unblock_user($user_id, $conn){
        $query = "UPDATE `user` SET is_enabled = 1 WHERE `id` = $user_id";
        $res = mysqli_query($conn, $query);
    }

}