<?php
require_once("common/commonFunctions.php");


$user=new stdClass();

$user->FirstName=VarExist($_POST["firstName"]);
$user->LastName=VarExist($_POST["lastName"]);
$user->Email=VarExist($_POST["email"]);
$user->Username=VarExist($_POST["username"]);
$user->Password=VarExist($_POST["password"]);
$user->University=VarExist($_POST["university"]);
$user->UniversityAcronym=VarExist($_POST["universityAcronym"]);
if (strlen($_POST["username"]) < 8 || strlen($_POST["password"]) < 8) {
    header("location:../html/auth-register-basic.html?alert=InvalidUsernameAndPassword");
    exit();
}
$db = DBConnect();

$checkUserExists = "SELECT Username FROM User WHERE Username = ?";
$stmt = $db->prepare($checkUserExists);
$stmt->execute([$user->Username]);
$existingUsername = $stmt->fetchColumn();

if ($existingUsername > 0){
    header("location:../html/auth-register-basic.html?alert=usernameExists");
    exit();
}
if (InsertUserToDBfromObject($user)){
    session_start();
    $row = $stmt->fetch();
    $id=$row["ID"];
    $_SESSION["id"]=$id;
    $_SESSION["username"]=$un;
    header("location:../landing.php");
}else{
    header("location:../index.php");
}


function InsertUserToDBfromObject($user){
    $db = DBConnect();

    // Prepare the query to check if the university exists
    $universityQuery = "SELECT UniversityId FROM University WHERE UniversityName = ?";
    $stmt = $db->prepare($universityQuery);
    $stmt->execute([$user->University]);
    $universityId = $stmt->fetchColumn();

    // If the university doesn't exist, create it and retrieve its ID
    if (!$universityId) {
        $insertUniversityQuery = "INSERT INTO University (UniversityName, UniversityAcronym) VALUES (?,?)";
        $stmt = $db->prepare($insertUniversityQuery);
        $stmt->execute([$user->University, $user ->UniversityAcronym]);
        $universityId = $db->lastInsertId();
    }

    // Prepare the query to insert the user into the User table
    $query = "INSERT INTO User (Username, Password, Email, FirstName, LastName, UniversityId) VALUES (?, MD5(?), ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$user->Username, $user->Password, $user->Email, $user->FirstName, $user->LastName, $universityId]);

    if ($stmt->rowCount() > 0){
        session_start();
        $_SESSION["id"] = $db->lastInsertId();
        $_SESSION["username"] = $user->Username;
        return true;
    } else {
        return false;
    }
}



?>