<?php
$token = $_POST['token'];
require_once("../BE/common/commonFunctions.php");
$db = DBConnect();
$token_hash = hash("sha256", $token);
echo $token . "\n" .$token_hash;

$stmt = $db->prepare("SELECT * FROM user WHERE ResetTokenHash = ?");

// Bind the token
$stmt->bindParam(1, $token_hash, PDO::PARAM_STR);

$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user === false) {
    echo "Token not found";
} else {
    if (strtotime($user["ResetTokenExpiry"]) <= time()) {
        echo "Token has expired";
    } else {
      print_r($user);
      $query = "UPDATE User SET Password = MD5(?), ResetTokenHash = NULL, ResetTokenExpiry = NULL WHERE ResetTokenHash = ?";
      $stmt = $db->prepare($query);

    // Bind parameters
    $stmt->execute([$_POST["password"], $token_hash]);

    // Execute update statement
    if ($stmt -> rowCount() > 0){
        header("location:../html/auth-login-basic.html");
    } 
    }
}
?>
