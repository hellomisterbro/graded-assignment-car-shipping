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
require_once("../protected/model/Passenger.php");


function unset_newride_data()
{
    unset($_POST["submit"]);
    unset($_POST["time"]);
    unset($_POST["endpoint"]);
    unset($_POST["startpoint"]);
    unset($_POST["note"]);
    unset($_POST["price"]);
    unset($_POST["place_qty"]);

}

$current_user = User::get_by_id($_SESSION["user"], $conn);

if (isset($_GET['value_key'])) {
    print "";
    $ride_id = $_GET['value_key'];
    Ride::join_ride($current_user->db_id, $ride_id, $conn);
}


if (isset($_POST["submit"])) {

    $start_point = GoogleApi::geocode($_POST['startpoint']);
    $end_point = GoogleApi::geocode($_POST['endpoint']);

    $start_point->db_id = Location::save_to_DB($conn, $start_point);
    $end_point->db_id = Location::save_to_DB($conn, $end_point);

    $ride = new Ride();
    $ride->exeptions_days = array();

    $count = 0;
    $key = "exdatepicker" . strval($count);
    while (isset($_POST[$key])) {
        $key = "exdatepicker" . strval($count);
        $ride->add_day($_POST[$key]);
        $count++;
    }

    array_pop($ride->exeptions_days);

    foreach ($ride->exeptions_days as $exeptions_day) {
        $s =  $exeptions_day->format("Y-m-d H:i:s");

    }
    $ride->user = $current_user;
    $ride->start_time = DateTime::createFromFormat("m/d/Y h:i A", $_POST["time"]);
    $ride->end_time = new DateTime();
    $ride->start_point = $start_point;
    $ride->end_point = $end_point;
    $ride->note = $_POST["note"];
    $ride->weekly = isset($_POST["weekly"])?0:1;
    $ride->price = doubleval($_POST["price"]);
    $ride->reservation_places = intval($_POST["place_qty"]);

    Ride::save_to_DB($conn, $ride);

    unset_newride_data();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User</title>
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

    <style type="text/css">


    </style>
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
<div class="container">

    <div class="container">
        <div class="row">

            <!-- profile image-->
            <div class="col-sm-3">
                <div class="container">
                    <br>
                    <img src="./img/content/user-blank.png" class="img-circle" alt="Cinque Terre" width="225"
                         height="225">
                    <h2><?php echo $current_user->name ?></h2>
                </div>
            </div>

            <!-- user information -->
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-1">
                        <h5>Name: </h5>

                    </div>
                    <div class="col-sm-11">
                        <h5><?php echo $current_user->name ?></h5>
                    </div>
                    <div class="col-sm-1">
                        <h5>About: </h5>

                    </div>
                    <div class="col-sm-11">
                        <h5><?php echo $current_user->desc ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br>
    <?php
    $arr = Ride::get_rides_for_user($current_user->db_id, $conn);
    $arr1 = Passenger::get_by_userid($current_user->db_id, $conn);
    foreach ($arr as &$ride) {
    ?>
    <!--    ride-->
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading"> <?php echo $ride->start_time->format("d M, Y") ?></div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="./img/content/user-blank.png" class="img-circle" alt="Cinque Terre" width="100"
                             height="100">
                        <h4><?php echo $ride->user->name ?></h4>
                        <?php
                        if ($ride->user->db_id == $current_user->db_id) {
                            ?>
                            <span class="label label-info">You are a driver</span>
                            <?php
                        } else {
                            ?>
                            <span class="label label-info">You are a passanger</span>
                            <?php
                        }
                        ?>
                        </div>
                        <div class="col-sm-8">
                            <h3><?php echo $ride->start_point->name ?> - <?php echo $ride->end_point->name ?>
                                ,<?php echo $ride->start_time->format("H:i") ?></h3>
                            <h4>approx. time of arrival ~ <?php echo $ride->end_time->format("H:i") ?> </h4>
                            <div class="row">
                                <div class="col-sm-1">
                                    <h5>Note:</h5>
                                </div>
                                <div class="col-sm-10">
                                    <h5><?php echo $ride->note ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <button type="text" class="btn btn-primary">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>


    <br>
    <br>
    <br>
    <!--    Adding a new ride-->
    <form method="post">
        <div class="container" id="newRide" style="display: none">
            <div class="panel panel-default">
                <div class="panel-heading"> New trip</div>
                <div class="panel-body">
                    <!--    Adding a new ride-->
                    <!--    Adding a datepicker-->
                    <div class="row">

                        <div class='col-sm-3'>
                            <div class="form-group">
                                <label>Departure time:</label>
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' name="time" class="form-control"/>
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

                    <!--    Weekly chrckbox-->
                    <form method="post">
                    <div class="checkbox">
                        <script>
                            $(document).ready(function () {
                                $('[data-toggle="popover"]').popover();
                            });
                        </script>
                        <label>
                            <input type="checkbox" name ="weekly" id="weekly_checkbox" value="">Weekly
                        </label>
                        (<a title="Info" data-toggle="popover" data-trigger="hover"
                            data-content="By checking it you state that you will do your ride every week">See info</a>)
                    </div>
                        </form>
                    <label>Add new exeption date:</label>

                    <div class="row">
                        <div class='col-sm-12'>
                            <div class='col-sm-6'>
                                <div class="mytemplate" style="display: none;padding: 10px 0px">
                                    <input class="datepicker" id="exdatepicker0" type="text" />
                                    <br>
                                </div>
                                <div class="dates" style="padding: 10px 0px">
                                    <div>
                                        <input class="datepicker" name="exdatepicker0" type="text"/>
                                    </div>
                                </div>
                                <input type="button" class="addmore" value="Add more">
                                <br><br>
                                <script>
                                    var idCount = 1;
                                    $(document).ready(function () {
                                        $('.addmore').on('click', function () {

                                            var element = $(".mytemplate").clone();
                                            element.removeClass("mytemplate").show().appendTo(".dates");
                                            console.log(element.children()[0]);
                                            var datepickerEl = element.children()[0];
                                            datepickerEl.name = "exdatepicker" + idCount;
                                            idCount++;
                                            ;
                                        });
                                        $(document).on("focus", ".datepicker", function () {
                                            $(this).datepicker();
                                        });
                                    });

                                </script>
                            </div>


                            <div class='col-sm-6'>
                                <div class="row">
                                    <div class='col-sm-6'>

                                    </div>


                                </div>

                            </div>
                            <div class='col-sm-4'></div>
                        </div>
                    </div>
                    <!--    Number of people in the car-->
                    <div class="form-group">
                        <label for="sel1">Select list:</label>
                        <select name="place_qty" class="form-control" id="sel1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                        </select>
                    </div>
                    <!--  A note for the trip-->
                    <div class="form-group">
                        <label for="comment">Please enter a note for your trip:</label>
                        <textarea class="form-control" name="note" rows="5" id="comment"
                                  style="resize: none"></textarea>
                    </div>

                    <!--   Price-->
                    <div class="form-group">
                        <label for="prc">Price per person (dollars):</label>
                        <input type="text" pattern=".{3,}" required title="3 characters minimum" name="price"
                               class="form-control number-only" id="prc">
                        <script>
                            $("input.number-only").bind({
                                keydown: function (e) {
                                    if (this.value.length > 5 && e.which != 8) {
                                        return false;
                                    }
                                    if (e.shiftKey === true) {
                                        if (e.which == 9) {
                                            return true;
                                        }
                                        return false;
                                    }
                                    if (e.which > 57) {
                                        return false;
                                    }
                                    if (e.which == 32) {
                                        return false;
                                    }
                                    return true;
                                }
                            });
                            $('input.number-only').keyup(function () {

                            });
                        </script>
                    </div>

                    <!--    Submit button-->
                    <div style="float: right;">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        <br>
                    </div>

                </div>
            </div>


        </div>
        <div style="float: right;">
            <button id="addBtn" type="button" class="btn btn-primary">Add new ride</button>
            <script>
                document.getElementById("addBtn").addEventListener("click", myFunction);

                function myFunction() {
                    document.getElementById("addBtn").style.display = "none";
                    document.getElementById("newRide").style.display = "block";
                    window.scrollBy(0, 1000)
                }
            </script>

            <br>
        </div>

    </form>
    <!--    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />-->
    <!--    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

</body>
</html>
