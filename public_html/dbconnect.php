<?php
/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 12:12 PM
 */

require_once( $_SERVER["DOCUMENT_ROOT"] . "/car-shipping/resources/config.php");

 // this will avoid mysql_connect() deprecation error.
 error_reporting( ~E_DEPRECATED & ~E_NOTICE );

 define('DBHOST', $config["db"]["host"]);
 define('DBUSER', $config["db"]["username"]);
 define('DBPASS', $config["db"]["password"]);
 define('DBNAME', $config["db"]["dbname"]);

 $conn = mysqli_connect(DBHOST,DBUSER,DBPASS, DBNAME);
 $dbcon = mysqli_select_db($conn, DBNAME);

 if ( !$conn ) {
     die("Connection failed : " . mysqli_error($conn));
 }

 if ( !$dbcon ) {
     die("Database Connection failed : " . mysqli_error($conn));
 }