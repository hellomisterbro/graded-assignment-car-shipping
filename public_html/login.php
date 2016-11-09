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
require_once("dbconnect.php");

function unset_userdata(){
    unset($_POST["email"]);
    unset($_POST["password"]);
}

if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $pass = $_POST["password"];
    $res = User::authorization($email, $pass, $conn);
    if (is_null($res)){
        $err = "Wrong password.";
        unset_userdata();
    } else {
        $_SESSION['user'] = $res;
        print $_SESSION["user"];
        header('Location: userpage.php');
        unset_userdata();
        exit();
    }


}


?>

<!DOCTYPE html>
<html lang="en">
<title>Login Page</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/background-image-form.css">
<link rel="stylesheet" href="../resources/library/bootstrap-3.3.7-dist/css/bootstrap.css">
<script src="../resources/library/jquery-3.1.1.min.js"></script>
<script src="../resources/library/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

<head>

    <img src="img/layout/road.jpg" class="bg" alt="">
    <h1 class="start-login"> Car Sharing System </h1>
    <div id="page-wrap-login">

        <h2>Please sign in </h2>
        <br>
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

        <form class="form-horizontal" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" name="password" class="form-control" id="pwd" placeholder="Enter password">
            </div>
<!--            <div class="checkbox">-->
<!--                <label><input type="checkbox">Remember me</label>-->
<!--            </div>-->
            <br>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="register.php"><span class="glyphicon glyphicon-log-in"></span> Register </a></li>
            </ul>
            <button type="submit" name="submit" class="btn btn-default">Submit</button>

        </form>


    </div>


</head>
<body>

</body>
</html>

