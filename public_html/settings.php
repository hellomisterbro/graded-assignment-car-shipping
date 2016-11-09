<?php
/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 11/2/16
 * Time: 8:39 PM
 */

ob_start();
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include_once("navbar.php");
require_once("dbconnect.php");
require_once("../protected/model/User.php");
require_once("../protected/API/ValidityHandler.php");

$success;
$err;
$current_user = User::get_by_id($_SESSION["user"], $conn);

if(isset($_SESSION["success"])){
    $success = "Information is updated successfully";
    unset($_SESSION["success"]);
}
if (isset($_POST["submit"])) {


    //uploading file to local folder
    $pic = rand(1000, 100000) . "-" . $_FILES['pic']['name'];

    $pic_loc = $_FILES['pic']['tmp_name'];
    $folder = "img/temp/";
    $full_path_img = $folder . $pic;
    $error_uplodaing_file = false;
    if (!move_uploaded_file($pic_loc, $folder . $pic)) {
        print "ERROR";
        $error_uplodaing_file = true;
    }

    $car = new Car();
    $car->name = $_POST["carname"];
    $car->num_places = intval($_POST["place_qty"]);
    $user = new User();

    $user->name = $_POST["username"];
    $user->db_id = $current_user->db_id;
    $user->email = $_POST["email"];
    $user->password = $_POST["password"];
    $user->phone = $_POST["phonenum"];
    $user->country = $_POST["country"];
    $user->car = $car;
    $user->photo_path = ($error_uplodaing_file || !$_FILES['pic']['name']) ?'':$full_path_img;
    $user->desc = $_POST["desc"] == '' ? 'Information is not defined.' : $_POST["desc"];

    if (ValidyHandler::checkName($user->name, $err)
    && ValidyHandler::checkEmail($user->email, $err)
    && ($user->password == '' ? true : (ValidyHandler::checkPassword($user->password, $err)))
        && ValidyHandler::checkPhone($user->phone, $err)
    ) {

        $res = User::update_DB($user, $conn);

        if ($res) {
            $success = "Your info is succesfully updated.";
            $_SESSION["success"] = true;
            header("Location: settings.php");
        }
    }


}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Registration Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--local-->
    <link rel="stylesheet" href="../resources/library/country-select-js-master/build/css/countrySelect.css">
    <link rel="stylesheet" href="css/background-image-form.css">
    <link rel="stylesheet" href="../resources/library/bootstrap-3.3.7-dist/css/bootstrap.css">
    <script src="../resources/library/jquery-3.1.1.min.js"></script>
    <script src="../resources/library/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

    <script src="../resources/library/country-select-js-master/build/js/countrySelect.min.js"></script>

</head>
<body>

<div class="container">

    <?php
    if (isset($success)) {

        ?>
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success.. </strong> <?php echo $success ?>
        </div>
        <?php
    }
    ?>

    <?php
    if (isset($err)) {

        ?>
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Oops.. </strong> <?php echo $err ?>
        </div>
        <?php
    }
    ?>

    <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="email">Name:</label>
            <input name="username" type="mail" class="form-control" value="<?php echo $current_user->name ?>"
                   id="nm" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo $current_user->email ?>"
                   id="email" placeholder="Enter email">
        </div>
        <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" name="password" class="form-control" id="pwd" placeholder="Enter password">
        </div>
        <div class="form-group">
            <label for="country">Country:</label><br>
            <input type="country_selector" name="country" value="<?php echo $current_user->country ?>"
                   class="form-control" id="country">
            <input type="hidden" name="countryCode" id="country_code">
        </div>
        <div class="form-group">
            <label for="pwd">Enter phone number:</label>
            <input type="phonenum" name="phonenum" value="<?php echo $current_user->phone ?>" class="form-control"
                   id="pn" placeholder="Enter phone number">
        </div>
        <script>
            $("#country").countrySelect();
        </script>

        <!--  About -->
        <div class="form-group">
            <label for="comment">Please tell something about yourself:</label>
            <textarea class="form-control"  name="desc" rows="5"
                      id="comment"
                      style="resize: none"><?php echo $current_user->desc ?></textarea>
        </div>
        <div class="form-group">
            <label for="comment">Select an image for your profile:</label>
            <input class="form-control" type="file" name="pic"/>
        </div>
        <div class="form-group">
            <label for="email">Name of the car:</label>
            <input name="carname" type="mail" class="form-control" value="<?php echo $current_user->car?$current_user->car->name:''?>" id="nm" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="email">Number of places in the car:</label>
            <select name="place_qty" class="form-control" id="sel1">
                <option <?php echo ($current_user->car->num_places == 1)?'selected':''?>>1</option>
                <option <?php echo ($current_user->car->num_places == 2)?'selected':''?>>2</option>
                <option <?php echo ($current_user->car->num_places == 3)?'selected':''?>>3</option>
                <option <?php echo ($current_user->car->num_places == 4)?'selected':''?>>4</option>
                <option <?php echo ($current_user->car->num_places == 5)?'selected':''?>>5</option>
            </select>
        </div>

        <!--    Submit button-->
        <div style="float: right;">
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            <br>
        </div>

    </form>


</div>

</body>
