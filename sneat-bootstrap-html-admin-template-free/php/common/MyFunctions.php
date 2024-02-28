<?php

$dbhost = "127.0.0.1";
$dbname = "EdShare";
$dbuser = "root";
$dbpass = "";
$db = null;
try {
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$un = $_POST["username"];      //define variable $un and assign the username sent from the form
$pass = $_POST["password"];

$query = "select ID from tbl_users where USERNAME='" . $un . "' AND PASSWORD=PASSWORD('" . $pass . "')";
//echo $query;
$stmt = $db->query($query);
$rowCount = $stmt->rowCount();
echo $rowCount;
if ($rowCount > 0) {
    session_start();
    $row = $stmt->fetch();
    $id = $row["ID"];
    $_SESSION["id"] = $id;
    $_SESSION["username"] = $un;
    header("location:../../landing.php");
} else {
    header("location:../../index.php");
}

?>