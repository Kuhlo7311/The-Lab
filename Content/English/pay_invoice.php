
<?php
session_start();

$host = "localhost";
$user = "root";
$psw = "yqt";
$db = "RecyclingDB";
$conn = mysqli_connect($host, $user, $psw, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["invoiceID"])) {
    $invoiceID = mysqli_real_escape_string($conn, $_POST["invoiceID"]);

    // Mark the invoice as paid
    $updateInvoiceQuery = $conn->prepare("UPDATE Invoice SET IsPaid = 'Yes' WHERE InvoiceID = ?");
    $updateInvoiceQuery->bind_param("i", $invoiceID);
    if ($updateInvoiceQuery->execute()) {
        echo "Invoice marked as paid successfully.";
        header("Location: history.php"); // Redirect to history.php
    } else {
        echo "Error updating invoice: " . $updateInvoiceQuery->error;
    }
    

    $updateInvoiceQuery->close();
} else {
    echo "Invalid request.";
}

mysqli_close($conn);
?>
