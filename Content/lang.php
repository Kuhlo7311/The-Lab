<?php
$filePath = explode("/", $_SERVER["SCRIPT_NAME"]);
$fileName = $filePath[4];
$language = $filePath[3];
session_start();

$connection = mysqli_connect("localhost", "root", "yqt", "RecyclingDB");

// Check for connection errors
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}



if (!isset($_SESSION["logged"])) {
    $_SESSION["logged"] = false;
} else {
    if ($_SESSION["logged"] == true) {
        $sql96 = $connection->prepare("SELECT UserType FROM RecyclingUser WHERE UserName = ?");

        // Check for preparation errors
        if (!$sql96) {
            die("Prepare failed: (" . $connection->errno . ") " . $connection->error);
        }

        $username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
        $sql96->bind_param("s", $username);
        $sql96->execute();
        $result2 = $sql96->get_result();
        $row = $result2->fetch_assoc();
        if ($row && isset($row['UserType'])) {
            $_SESSION['user_type'] = $row['UserType'];
        }
    }
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = "en";
} elseif (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
    if ($_GET['lang'] == "en") {
        $_SESSION['lang'] = "en";
    } else {
        $_SESSION['lang'] = "de";
    }
}

require_once $_SESSION['lang'] . ".php";
?>
