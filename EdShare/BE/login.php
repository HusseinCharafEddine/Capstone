<?php
require_once ("common/commonFunctions.php");

$db = DBConnect();
$un = $_POST["username"];
$pass = $_POST["password"];

$query = "select UserId from user where Username='" . $un . "' AND Password=MD5('" . $pass . "')";
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
    header("location:../landing.php");
} else {
    session_start();
    session_unset();
    session_destroy();
    header("location:../html/auth-login-basic.html?alert=invalidLogIn");

}

?>