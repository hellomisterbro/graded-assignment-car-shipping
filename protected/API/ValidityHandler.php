<?php

/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 11:04 PM
 */
class ValidyHandler
{

    public static function checkPassword($password, &$error)
    {
        if (empty($password)) {
            $error = "Password field is empty.";
            return false;
        }
        if (strlen($password) < 6) {
            $error = "Password is too short.";
            return false;
        }

        return true;
    }

    public static function checkEmail($email, &$error)
    {
        if (empty($email)) {
            $error = "Email field is empty.";
            return false;
        }
        return true;
    }

    public static function checkNumber($number, &$error)
    {
        if (empty($number)) {
            $error = "Number field is empty.";
            return false;
        }
        return true;
    }

    public static function checkName($name, &$error)
    {
        if (empty($name)) {
            $error = "Name field is empty.";
            return false;
        }

        if (strlen($name) < 6) {
            $error = "Name is too short.";
            return false;
        }
        return true;
    }

    public static function checkPhone($phone, &$error)
    {
        if (empty($phone)) {
            $error = "Phone field is empty.";
            return false;
        }
        return true;
    }

}