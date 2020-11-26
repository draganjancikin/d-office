<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/dbConfig.php';
session_start();

$table = "admin";    // the table that this script will set up and use.

// Create connection
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// echo "Connected successfully";

$username = $_POST["username"]; //Storing username in $username variable.
$password = $_POST["password"]; //Storing password in $password variable.

$match = "select id from $table where username = '".$_POST['username']."'
and password = '".$_POST['password']."'";

$result = mysqli_query($mysqli, $match);
$num_rows = mysqli_num_rows($result);


if ($num_rows <= 0) {
    // redirekcija ako nije dobar user
    header('location:../index.php?nouser');

    exit();

} else {

    $result_user = mysqli_query($mysqli, "SELECT * FROM admin WHERE username='$username' ") or die(mysqli_error($mysqli));

    $row_user = mysqli_fetch_array($result_user);	
    $user_id = $row_user['id'];
    $user_level = $row_user['level'];

    $_SESSION['username'] = $_POST["username"];
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_level'] = $user_level;

    header('location:../index.php');
    // It is the page where you want to redirect user after login.
}
