<?php
function VarExist($var)
{
    if (isset($var)) {
        return $var;
    } else {
        header("location:../index.html");
    }
}


function DBConnect()
{
    $dbhost = "127.0.0.1";
    $dbname = "edshare";
    $dbuser = "root";
    $dbpass = "";
    $db = null;
    try {
        $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
        $db = null;
    }
    return $db;
}

?>