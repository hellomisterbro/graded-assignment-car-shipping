<!--HI EVERYBODY!-->
<?php

/*

*/

$config = array(
    "db" => array(
        "dbname" => "DB_0160737444",
        "username" => "project",
        "password" => "project",
        "host" => "mysql"
    ),
    "urls" => array(
        "baseUrl" => "http://localhost/car-shipping"
    ),
    "paths" => array(
        "resources" => $_SERVER["DOCUMENT_ROOT"] . "/car-shipping/resources",
        "images" => array(
            "content" => $_SERVER["DOCUMENT_ROOT"] . "/images/content",
            "layout" => $_SERVER["DOCUMENT_ROOT"] . "/images/layout"
        )
    )
);


/*
    Creating constants for heavily used paths makes things a lot easier.
    ex. require_once(LIBRARY_PATH . "Paginator.php")
*/
defined("LIBRARY_PATH")
or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));

/*
    Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);

?>