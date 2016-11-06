<?php
/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 12:27 PM
 */

ob_start();
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include_once("navbar.php");
require_once("../protected/model/Location.php");
require_once("../protected/model/Ride.php");
require_once("../protected/model/User.php");
require_once("../protected/API/GoogleApi.php");
require_once("dbconnect.php");

function unset_newride_data()
{
    unset($_POST["search"]);
    unset($_POST["startpoint"]);
    unset($_POST["endpoint"]);
    unset($_POST["time1"]);
    unset($_POST["time2"]);
    unset($_POST[""]);
    unset($_POST[""]);

}


$current_user = User::get_by_id($_SESSION["user"], $conn);
$rides = array();
if (isset($_POST["search"])) {
    print $_POST["startpoint"];
    print $_POST["endpoint"];
    print $_POST["time1"];
    print $_POST["time2"];

    $start_point = GoogleApi::geocode($_POST["startpoint"]);
    $end_point = GoogleApi::geocode($_POST["endpoint"]);
    $start_time = DateTime::createFromFormat("m/d/Y h:i A", $_POST["time1"]);
    $end_time = DateTime::createFromFormat("m/d/Y h:i A", $_POST["time2"]);

    $rides = Ride::get_indentical_locations($start_point->lat, $end_point->lat,
        $start_point->lg, $end_point->lg,$start_time, $end_time , $conn);



    foreach ($rides as $ride) {
        print $ride->start_point->name;
        print $ride->end_point->name;
        print $ride->db_id;

    }

    unset_newride_data();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="../resources/library/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css"/>

    <script src="../resources/library/jquery-3.1.1.min.js"></script>
    <script src="../resources/library/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="../resources/library/bootstrap-sass-master/assets/javascripts/bootstrap/transition.js"></script>
    <script src="../resources/library/bootstrap-sass-master/assets/javascripts/bootstrap/collapse.js"></script>
    <script src="../resources/library/moment-master/moment.js"></script>
    <script src="../resources/library/bootstrap-datetimepicker-master/src/js/bootstrap-datetimepicker.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyARNH2967Fosb0h9IQsVeh47AAT5FfY6EY"
            type="text/javascript"></script>
    <script type="text/javascript">
        function initialize_start() {
            var input = document.getElementById('searchTextFieldStart');
            var autocomplete = new google.maps.places.Autocomplete(input);
        }
        google.maps.event.addDomListener(window, 'load', initialize_start);
        function initialize_end() {
            ;
            var input = document.getElementById('searchTextFieldEnd');
            var autocomplete = new google.maps.places.Autocomplete(input);
        }
        google.maps.event.addDomListener(window, 'load', initialize_end);
    </script>


    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
<div class="container">

    <br>
    <br>
    <br>
    <!--    Adding a new ride-->
    <form method="post">
        <div class="container" id="newRide">
            <div class="panel panel-default">
                <div class="panel-heading"></div>
                <div class="panel-body">
                    <!--    Adding a new ride-->


                    <!--    Adding a start point input usgin Google Api-->
                    <div class="form-group">
                        <label for="pwd">Start point:</label>
                        <input id="searchTextFieldStart" name="startpoint" class="form-control" type="text" size="50"
                               placeholder="Enter a location" autocomplete="on">
                        <!--                        <span class="glyphicon glyphicon-map-marker"></span>-->
                    </div>

                    <!--    Adding an end point input usgin Google Api-->
                    <div class="form-group">
                        <label for="pwd">End point:</label>
                        <input id="searchTextFieldEnd" name="endpoint" class="form-control" type="text" size="50"
                               placeholder="Enter a location" autocomplete="on">
                        <!--                        <span class="glyphicon glyphicon-map-marker"></span>-->
                    </div>

                    <!--   Date picker 1-->
                    <div class="row">

                        <div class='col-sm-3'>
                            <div class="form-group">
                                <label>Departure time from:</label>
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' name="time1" class="form-control"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                   </span>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#datetimepicker1').datetimepicker();
                            });
                        </script>
                    </div>


                    <!--   Date picker 2-->
                    <div class="row">

                        <div class='col-sm-3'>
                            <div class="form-group">
                                <label>Departure time to:</label>
                                <div class='input-group date' id='datetimepicker2'>
                                    <input type='text' name="time2" class="form-control"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                   </span>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#datetimepicker2').datetimepicker();
                            });
                        </script>
                    </div>


                    <!--    Submit button-->
                    <div style="float: right;">
                        <button type="search" name="search" class="btn btn-primary">Search</button>
                        <br>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <?php

    foreach ($rides as &$ride) {


        ?>
        <div class="container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo $ride->start_time->format("d M, Y") ?>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="./img/content/user-blank.png" class="img-circle" alt="Cinque Terre" width="100"
                                 height="100">
                            <h4><?php echo $ride->user->name ?></h4>

                        </div>
                        <div class="col-sm-8">
                            <h3><?php echo $ride->start_point->name ?> - <?php echo $ride->end_point->name ?>
                                ,<?php echo $ride->start_time->format("H:i") ?></h3>

                            <h4>approx. time of arrival ~ <?php echo $ride->end_time->format("H:i") ?> </h4>

                            <div class="row">
                                <div class="col-sm-1">
                                    <h5> Note:</h5>
                                </div>
                                <div class="col-sm-10">
                                    <h5><?php echo $ride->note ?></h5>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-1">
                                    <h5>Places left:</h5>
                                </div>
                                <div class="col-sm-10">
                                    <h5><?php echo $ride->reservation_places ?></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-1">
                                    <h5>weekly:</h5>
                                </div>
                                <div class="col-sm-10">
                                    <h5><?php echo  $ride->weekly?"YES":"NO"?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <button type="text" class="btn btn-primary" onclick="location.href='userpage.php?value_key=<?php echo $ride->db_id?>'">Join</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

</body>
</html>
