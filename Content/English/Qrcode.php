<?php

// Start or resume the session
session_start();

// Set up database connection
$host = "localhost";
$user = "root";
$psw = "";
$db = "RecyclingDB";
$conn = mysqli_connect($host, $user, $psw, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include the necessary QR code generation code
include("../phpqrcode/qrlib.php");

// Set content type to image/png
header('Content-Type: image/png');
//QRcode::png('Test', false, 'L', 10, 2);

// Check if the user is logged in
if (isset($_SESSION["username"])) {
    $Username = $_SESSION["username"];
    
    // Use prepared statements to prevent SQL injection
    $select = $conn->prepare("SELECT RandomCode FROM RecyclingUser WHERE UserName = ?");
    $select->bind_param("s", $Username);
    $select->execute();
    
    // Check for errors in the query execution
    if (!$select->errno) {
        $result = $select->get_result();
        
        // Check if the user exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            QRcode::png($row["RandomCode"], false, 'L', 10, 2);
            //QRcode::png('Test', false, 'L', 10, 2);
        } else {
            echo "User not found.";
        }
    } else {
        echo "Query execution error.";
    }
} else {
    echo "Session variable is not set.";
}

// Close the database connection
mysqli_close($conn);
?>
