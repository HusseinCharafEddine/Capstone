<?php
require_once("common/commonFunctions.php");

$db = DBConnect();
$email = $_POST["email"];

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "UPDATE user
        SET ResetTokenHash = ?,
            ResetTokenExpiry = ?
        WHERE email = ?";

$stmt = $db->prepare($sql);

$stmt->execute([$token_hash, $expiry, $email]);

if ($stmt->rowCount() > 0) {

    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("hu.sharafeddine@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
    Click <a href="http://capstone/EdShare/html/auth-password-reset-page.php?token=$token">here</a> 
    to reset your password.
    END;

    try {

        $mail->send();

    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        return;
    }

}

echo "Message sent, please check your inbox.";
header("location:../html/auth-email-sent.html");
?>
