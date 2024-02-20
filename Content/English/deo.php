<?php
session_start();

$host = "localhost";
$user = "Kuhlo731";
$psw = "yqt";
$db = "RecyclingDB";
$conn = mysqli_connect($host, $user, $psw, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["username"]) || $_SESSION["userType"] !== "admin") {
    header("Location: unauthorized_access.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["weight"], $_POST["station"])) {
    $weight = mysqli_real_escape_string($conn, $_POST["weight"]);
    $stationID = mysqli_real_escape_string($conn, $_POST["station"]);
    $userID = $_SESSION["UserID"]; // Assuming you store user ID in the session

    // Get the waste type based on the selected station
    $getTypeQuery = mysqli_query($conn, "SELECT TypeID FROM MeasurementStation WHERE StationID = $stationID");
    $rowType = mysqli_fetch_assoc($getTypeQuery);
    $typeID = $rowType['TypeID'];

    // Insert the new trash deposit into the database
    $insertQuery = $conn->prepare("INSERT INTO WasteMeasurement (Weight, DateMeasure, InvoiceID, StationID, TypeID, UserID) VALUES (?, NOW(), null, ?, ?, ?)");
    $insertQuery->bind_param("diii", $weight, $stationID, $typeID, $userID);

    if ($insertQuery->execute()) {
        // Calculate and create invoice instantly
        $measurementID = mysqli_insert_id($conn);

        // Retrieve the waste type price
        $resultType = mysqli_query($conn, "SELECT * FROM TypeWaste WHERE TypeID = $typeID");
        $rowType = mysqli_fetch_assoc($resultType);

        // Calculate the invoice amount in cents (assuming the price is in cents per gram)
        $weightInGrams = $weight * 1000; // Convert weight to grams
        $invoiceAmount = $weightInGrams * $rowType['Price'];

        // Insert the invoice record with InvoiceID = 0 initially
        $dateInvoice = date("Y-m-d H:i:s");
        $isPaid = "unpaid";
        $toPayAmount = $invoiceAmount / 100; // Convert cents to dollars

        $insertInvoice = $conn->prepare("INSERT INTO Invoice (DateInvoice, IsPaid, ToPay) VALUES (?, ?, ?)");
        $insertInvoice->bind_param("sss", $dateInvoice, $isPaid, $toPayAmount);

        if ($insertInvoice->execute()) {
            $invoiceID = mysqli_insert_id($conn);

            // Update the WasteMeasurement record with the invoice ID
            $updateMeasurement = "UPDATE WasteMeasurement SET InvoiceID = $invoiceID WHERE MeasurementID = $measurementID";
            if (mysqli_query($conn, $updateMeasurement)) {
                echo "Deposit and Invoice created successfully.";
            } else {
                echo "Error updating WasteMeasurement record: " . mysqli_error($conn);
            }
        } else {
            echo "Error creating invoice: " . $insertInvoice->error;
        }
    } else {
        echo "Error creating deposit: " . $insertQuery->error;
    }

    $insertQuery->close();
}

// Retrieve measurement stations for the dropdown
$resultStations = mysqli_query($conn, "SELECT * FROM MeasurementStation");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        section {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            width: 100%;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
            padding: 10px;
            border: none;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="../style.scss">
</head>

<body>
    <header>
        <img src="../Media/Recy.png" alt="logo" style="width: 100px; height: 100px; float: center">
    </header>

    <nav>
        <?php include '../Navbar.php'; ?>
    </nav>

    <section>
        <h2>Create Trash Deposit</h2>

        <!-- Create Deposit Form -->
        <form method="POST" action="">
            <label for="weight">Weight (kg):</label>
            <input type="number" step="0.01" name="weight" required><br>

            <!-- New dropdown for selecting the measurement station -->
            <label for="station">Measurement Station:</label>
            <select name="station" required>
                <?php
                while ($rowStation = mysqli_fetch_assoc($resultStations)) {
                    echo "<option value='{$rowStation['StationID']}'>{$rowStation['Description']}</option>";
                }
                ?>
            </select><br>

            <input type="submit" value="Create Deposit">
        </form>
    </section>

    <footer>
        <p><b><i>©Loan Kuhlmann 2024</i></b></p>
    </footer>

</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
