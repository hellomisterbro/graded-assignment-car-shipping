<?php
/**
 * Created by PhpStorm.
 * User: kirichek
 * Date: 10/26/16
 * Time: 12:05 PM
 */

ob_start();
session_start();


require_once("../protected/model/User.php");
require_once("../protected/API/ValidityHandler.php");
require_once("dbconnect.php");

function unset_userdata(){
    unset($_POST["username"]);
    unset($_POST["email"]);
    unset($_POST["password"]);
    unset($_POST["phonenum"]);
    unset($_POST["country"]);
}

unset($_SESSION['user']);

if (isset($_POST["submit"])) {
    $success;
    $err;

    $user = new User();

    $user->name = $_POST["username"];
    $user->email = $_POST["email"];
    $user->password = $_POST["password"];
    $user->phone = $_POST["phonenum"];
    $user->country = $_POST["country"];
    $user->desc = 'Information is not defined.';

    if (ValidyHandler::checkName($user->name, $err)
        && ValidyHandler::checkEmail($user->email, $err)
        && ValidyHandler::checkPassword($user->password, $err)
        && ValidyHandler::checkPhone($user->phone, $err)) {
        $res = User::save_to_DB($conn, $user);
        if($res) {
            $success = "We send a requast to the site administrator. ";
        } else {
            $err = "Oops.. Something happend. Try again";
        }
    }
    unset_userdata();
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

<img src="img/layout/road.jpg" class="bg" alt="">
<h1 class="start-register"> Car Sharing System </h1>
<div id="page-wrap-register">

    <h2>Please register here </h2>

    <br>


    <?php
    if ( isset($success) ) {

        ?>
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success.. </strong> <?php echo $success?>
        </div>
        <?php
    }
    ?>

    <?php
    if ( isset($err) ) {

        ?>
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Oops..  </strong> <?php echo $err?>
        </div>
        <?php
    }
    ?>

    <form class="form-horizontal" method="post">
        <div class="form-group">
            <label for="email">Name:</label>
            <input name="username"  type="email" class="form-control" id="nm" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
        </div>
        <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" name="password" class="form-control" id="pwd" placeholder="Enter password">
        </div>
        <div class="form-group">
            <label for="country">Country:</label><br>
            <input type="country_selector"  name="country" class="form-control" id="country">
            <input type="hidden" name="countryCode" id="country_code">
        </div>
        <div class="form-group">
            <label for="pwd">Enter phone number:</label>
            <input type="phonenum" name="phonenum" class="form-control" id="pn" placeholder="Enter phone number">
        </div>

        <script>
            $("#country").countrySelect();
        </script>

        <br>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login </a></li>
        </ul>
        <form method='post'>
            <button type="submit" name="submit" class="btn btn-default">Submit</button>
        </form>
    </form>

</div>
</body>
</html>

