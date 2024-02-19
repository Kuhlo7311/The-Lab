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

// Check if the user is logged in
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];

    // Retrieve information for the logged-in user
    $resultUser = mysqli_query($conn, "SELECT * FROM RecyclingUser WHERE UserName = '$username'");
    $rowUser = mysqli_fetch_assoc($resultUser);

    // Retrieve all invoices for the user with additional information
    $userID = $rowUser['UserID'];
    $resultInvoices = mysqli_query($conn, "SELECT i.*, wm.Weight, wm.TypeID, tw.Description AS TypeDescription,
                                                ms.Description AS StationDescription, ms.Address AS StationAddress
                                            FROM Invoice i
                                            JOIN WasteMeasurement wm ON i.InvoiceID = wm.InvoiceID
                                            JOIN TypeWaste tw ON wm.TypeID = tw.TypeID
                                            JOIN MeasurementStation ms ON wm.StationID = ms.StationID
                                            WHERE wm.UserID = $userID
                                            ORDER BY i.DateInvoice DESC");

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

                p {
                    margin: 0;
                }

                hr {
                    margin: 10px 0;
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
            <h2>Your Deposit History</h2>

            <?php
            // Display all invoices in the history
            while ($rowInvoice = mysqli_fetch_assoc($resultInvoices)) {
                echo "<p><b>Date & Time of Deposit:</b> " . $rowInvoice['DateInvoice'] . "</p>";
                echo "<p><b>Weight Deposited:</b> " . $rowInvoice['Weight'] . " kg</p>";
                echo "<p><b>Waste Type:</b> " . $rowInvoice['TypeDescription'] . "</p>";
                echo "<p><b>Paid:</b> " . $rowInvoice['ToPay'] . "€</p>";
                echo "<p><b>Recycling Center:</b> " . $rowInvoice['StationAddress'] . "</p>";
                echo "<p><b>Measurement Station:</b> " . $rowInvoice['StationDescription'] . "</p>";

                // Check if the invoice is already paid
                if ($rowInvoice['IsPaid'] === 'Yes') {
                    echo "<p><b>Payment Status:</b> Paid</p>";
                }

                echo "<hr>";
            }
            ?>
        </section>

        <footer>
                <p><b><i>©Loan Kuhlmann 2024</i></b></p>
            </footer>

    </body>

    </html>

    <?php
} else {
    // Redirect to login page or handle the case when the user is not logged in
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
