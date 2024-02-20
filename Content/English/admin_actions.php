<?php
session_start(); // Start the session

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "Kuhlo731";
$psw = "yqt";
$db = "RecyclingDB";
$conn = mysqli_connect($host, $user, $psw, $db);

// Check for database connection errors
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["username"]) || $_SESSION["userType"] !== "admin") {
    header("Location: unauthorized_access.php");
    exit();
}

// Handle adding a recycling center
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_center_name"], $_POST["add_center_address"])) {
    $centerName = mysqli_real_escape_string($conn, $_POST["add_center_name"]);
    $centerAddress = mysqli_real_escape_string($conn, $_POST["add_center_address"]);

    // Use prepared statement to prevent SQL injection
    $query = $conn->prepare("INSERT INTO RecyclingCenter (CenterName, Address) VALUES (?, ?)");
    $query->bind_param("ss", $centerName, $centerAddress);

    if ($query->execute()) {
        echo "Recycling Center added successfully.";
    } else {
        echo "Error: " . $query->error;
    }

    $query->close();
}

// Handle editing a recycling center
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_center_id"], $_POST["edit_center_name"])) {
    $centerId = mysqli_real_escape_string($conn, $_POST["edit_center_id"]);
    $newCenterName = mysqli_real_escape_string($conn, $_POST["edit_center_name"]);

    // Use prepared statement to prevent SQL injection
    $query = $conn->prepare("UPDATE RecyclingCenter SET CenterName = ? WHERE CenterID = ?");
    $query->bind_param("si", $newCenterName, $centerId);

    if ($query->execute()) {
        echo "Recycling Center edited successfully.";
    } else {
        echo "Error: " . $query->error;
    }

    $query->close();
}

// Handle deleting a recycling center
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_center_id"])) {
    $centerId = mysqli_real_escape_string($conn, $_POST["delete_center_id"]);

    // Check if there are related users
    $checkUsersQuery = $conn->prepare("SELECT * FROM RecyclingUser WHERE FavoriteCenter = ?");
    $checkUsersQuery->bind_param("i", $centerId);
    $checkUsersQuery->execute();
    $result = $checkUsersQuery->get_result();

    if ($result->num_rows > 0) {
        echo "Error: Cannot delete recycling center. Users are associated with this center.";
    } else {
        // Use prepared statement to prevent SQL injection
        $deleteQuery = $conn->prepare("DELETE FROM RecyclingCenter WHERE CenterID = ?");
        $deleteQuery->bind_param("i", $centerId);

        if ($deleteQuery->execute()) {
            echo "Recycling Center deleted successfully.";
        } else {
            echo "Error: " . $deleteQuery->error;
        }

        $deleteQuery->close();
    }

    $checkUsersQuery->close();
}

// Handle adding a waste type
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_type_description"], $_POST["add_type_price"])) {
    $typeDescription = mysqli_real_escape_string($conn, $_POST["add_type_description"]);
    $typePrice = mysqli_real_escape_string($conn, $_POST["add_type_price"]);

    // Use prepared statement to prevent SQL injection
    $typeQuery = $conn->prepare("INSERT INTO TypeWaste (Description, Price) VALUES (?, ?)");
    $typeQuery->bind_param("sd", $typeDescription, $typePrice);

    if ($typeQuery->execute()) {
        echo "Waste Type added successfully.";
    } else {
        echo "Error: " . $typeQuery->error;
    }

    $typeQuery->close();
}

// Handle editing a waste type
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_type_id"], $_POST["edit_type_description"], $_POST["edit_type_price"])) {
    $typeId = mysqli_real_escape_string($conn, $_POST["edit_type_id"]);
    $newTypeDescription = mysqli_real_escape_string($conn, $_POST["edit_type_description"]);
    $newTypePrice = mysqli_real_escape_string($conn, $_POST["edit_type_price"]);

    // Use prepared statement to prevent SQL injection
    $editTypeQuery = $conn->prepare("UPDATE TypeWaste SET Description = ?, Price = ? WHERE TypeID = ?");
    $editTypeQuery->bind_param("sdi", $newTypeDescription, $newTypePrice, $typeId);

    if ($editTypeQuery->execute()) {
        echo "Waste Type edited successfully.";
    } else {
        echo "Error: " . $editTypeQuery->error;
    }

    $editTypeQuery->close();
}


// Handle deleting a waste type
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_type_id"])) {
    $typeId = mysqli_real_escape_string($conn, $_POST["delete_type_id"]);

    // Output POST data for debugging
    echo "POST data: ";
    print_r($_POST);

    // Use prepared statement to prevent SQL injection
    $deleteTypeQuery = $conn->prepare("DELETE FROM TypeWaste WHERE TypeID = ?");
    $deleteTypeQuery->bind_param("i", $typeId);

    // Output the result of the deletion query
    echo "Delete Query Result: " . ($deleteTypeQuery->execute() ? "Success" : "Failure");

    $deleteTypeQuery->close();
}

// Handle adding a measurement station
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_station_description"], $_POST["add_station_center"], $_POST["add_station_type"])) {
    $stationDescription = mysqli_real_escape_string($conn, $_POST["add_station_description"]);
    $stationCenter = mysqli_real_escape_string($conn, $_POST["add_station_center"]);
    $stationType = mysqli_real_escape_string($conn, $_POST["add_station_type"]);

    // Use prepared statement to prevent SQL injection
    $addStationQuery = $conn->prepare("INSERT INTO MeasurementStation (Description, CenterID, TypeID) VALUES (?, ?, ?)");
    $addStationQuery->bind_param("sii", $stationDescription, $stationCenter, $stationType);

    if ($addStationQuery->execute()) {
        echo "Measurement Station added successfully.";
    } else {
        echo "Error: " . $addStationQuery->error;
    }

    $addStationQuery->close();
}

// Handle editing a measurement station
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_station_id"], $_POST["edit_station_description"], $_POST["edit_station_center"], $_POST["edit_station_type"])) {
    $stationId = mysqli_real_escape_string($conn, $_POST["edit_station_id"]);
    $newStationDescription = mysqli_real_escape_string($conn, $_POST["edit_station_description"]);
    $newStationCenter = mysqli_real_escape_string($conn, $_POST["edit_station_center"]);
    $newStationType = mysqli_real_escape_string($conn, $_POST["edit_station_type"]);

    // Use prepared statement to prevent SQL injection
    $editStationQuery = $conn->prepare("UPDATE MeasurementStation SET Description = ?, CenterID = ?, TypeID = ? WHERE StationID = ?");
    $editStationQuery->bind_param("siii", $newStationDescription, $newStationCenter, $newStationType, $stationId);

    if ($editStationQuery->execute()) {
        echo "Measurement Station edited successfully.";
    } else {
        echo "Error: " . $editStationQuery->error;
    }

    $editStationQuery->close();
}

// Handle deleting a measurement station
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_station_id"])) {
    $stationId = mysqli_real_escape_string($conn, $_POST["delete_station_id"]);

    // Use prepared statement to prevent SQL injection
    $deleteStationQuery = $conn->prepare("DELETE FROM MeasurementStation WHERE StationID = ?");
    $deleteStationQuery->bind_param("i", $stationId);

    if ($deleteStationQuery->execute()) {
        echo "Measurement Station deleted successfully.";
    } else {
        echo "Error: " . $deleteStationQuery->error;
    }

    $deleteStationQuery->close();
}

?>


<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <style>
        /* Include your styles here */
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

        input, select {
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
     <link rel="stylesheet" href="../style.scss" time(): int>
</head>

<body>
    <header>
        <img src="../Media/Recy.png" alt="logo" style="width: 100px; height: 100px; float: center">
    </header>

    <nav>
        <?php include '../Navbar.php'; ?>
    </nav>

    <section>
    <form method="post" action="deo.php" style="display: inline;">
                <button type="submit" class="button">Access temporairy deposit form</button>


                
</form>

        <h2>Manage Recycling Centers</h2>

        <!-- Add Recycling Center Form -->
        <h3>Add Recycling Center</h3>
        <form method="POST" action="admin_actions.php">
            <label for="add_center_name">Center Name:</label>
            <input type="text" name="add_center_name" required><br>
            <label for="add_center_address">Address:</label>
            <input type="text" name="add_center_address" required><br>
            <input type="submit" value="Add Recycling Center">
        </form>

        <!-- Edit Recycling Center Form -->
        <h3>Edit Recycling Center</h3>
        <form method="POST" action="admin_actions.php">
            <label for="edit_center_id">Select Center:</label>
            <select name="edit_center_id">
                <?php
                $resultCenters = mysqli_query($conn, "SELECT * FROM RecyclingCenter");

                while ($rowCenter = mysqli_fetch_assoc($resultCenters)) {
                    echo "<option value='{$rowCenter['CenterID']}'>{$rowCenter['CenterName']}</option>";
                }
                ?>
            </select><br>
            <label for="edit_center_name">New Center Name:</label>
            <input type="text" name="edit_center_name" required><br>
            <input type="submit" value="Edit Recycling Center">
        </form>

        <!-- Delete Recycling Center Form -->
        <h3>Delete Recycling Center</h3>
        <form method="POST" action="admin_actions.php">
            <label for="delete_center_id">Select Center:</label>
            <select name="delete_center_id">
                <?php
                $resultCenters = mysqli_query($conn, "SELECT * FROM RecyclingCenter");

                while ($rowCenter = mysqli_fetch_assoc($resultCenters)) {
                    echo "<option value='{$rowCenter['CenterID']}'>{$rowCenter['CenterName']}</option>";
                }
                ?>
            </select><br>
            <input type="submit" value="Delete Recycling Center">
        </form>
    </section>

    <section>
        <h2>Manage Waste Types</h2>

        <!-- Add Waste Type Form -->
        <h3>Add Waste Type</h3>
        <form method="POST" action="admin_actions.php">
            <label for="add_type_description">Type Name:</label>
            <input type="text" name="add_type_description" required><br>
            <label for="add_type_price">Waste Type Price:</label>
            <input type="text" name="add_type_price" required><br>
            <input type="submit" value="Add Waste Type">
        </form>

        <!-- Edit Waste Type Form -->
        <h3>Edit Waste Type</h3>
        <form method="POST" action="admin_actions.php">
            <label for="edit_type_id">Select Type:</label>
            <select name="edit_type_id">
                <?php
                $resultTypes = mysqli_query($conn, "SELECT * FROM TypeWaste");

                while ($rowType = mysqli_fetch_assoc($resultTypes)) {
                    echo "<option value='{$rowType['TypeID']}'>{$rowType['Description']}</option>";
                }
                ?>
            </select><br>
            <label for="edit_type_description">New Type Name:</label>
            <input type="text" name="edit_type_description" required><br>
            <label for="edit_type_price">New Type Price:</label>
            <input type="text" name="edit_type_price" required><br>
            <input type="submit" value="Edit Waste Type">
        </form>

        <!-- Delete Waste Type Form -->
        <h3>Delete Waste Type</h3>
        <form method="POST" action="admin_actions.php">
    <label for="delete_type_id">Select Type:</label>
    <select name="delete_type_id">
        <?php
        $resultTypes = mysqli_query($conn, "SELECT * FROM TypeWaste");

        while ($rowType = mysqli_fetch_assoc($resultTypes)) {
            echo "<option value='{$rowType['TypeID']}'>{$rowType['Description']}</option>";
        }
        ?>
    </select><br>
    <input type="submit" value="Delete Waste Type">
</form>
    </section>

    <section>
<!-- Add Measurement Station Form -->
<h3>Add Measurement Station</h3>
<form method="POST" action="admin_actions.php">
    <label for="add_station_description">Station Name:</label>
    <input type="text" name="add_station_description" required><br>
    <label for="add_station_center">Select Center:</label>
    <select name="add_station_center">
        <?php
        $resultCenters = mysqli_query($conn, "SELECT * FROM RecyclingCenter");

        while ($rowCenter = mysqli_fetch_assoc($resultCenters)) {
            echo "<option value='{$rowCenter['CenterID']}'>{$rowCenter['CenterName']}</option>";
        }
        ?>
    </select><br>
    <label for="add_station_type">Select Waste Type:</label>
    <select name="add_station_type">
        <?php
        $resultTypes = mysqli_query($conn, "SELECT * FROM TypeWaste");

        while ($rowType = mysqli_fetch_assoc($resultTypes)) {
            echo "<option value='{$rowType['TypeID']}'>{$rowType['Description']}</option>";
        }
        ?>
    </select><br>
    <input type="submit" value="Add Measurement Station">
</form>

<!-- Edit Measurement Station Form -->
<h3>Edit Measurement Station</h3>
<form method="POST" action="admin_actions.php">
    <label for="edit_station_id">Select Station:</label>
    <select name="edit_station_id">
        <?php
        $resultStations = mysqli_query($conn, "SELECT * FROM MeasurementStation");

        while ($rowStation = mysqli_fetch_assoc($resultStations)) {
            echo "<option value='{$rowStation['StationID']}'>{$rowStation['Description']}</option>";
        }
        ?>
    </select><br>
    <label for="edit_station_description">New Station Name:</label>
    <input type="text" name="edit_station_description" required><br>
    <label for="edit_station_center">Select Center:</label>
    <select name="edit_station_center">
        <?php
        $resultCenters = mysqli_query($conn, "SELECT * FROM RecyclingCenter");

        while ($rowCenter = mysqli_fetch_assoc($resultCenters)) {
            echo "<option value='{$rowCenter['CenterID']}'>{$rowCenter['CenterName']}</option>";
        }
        ?>
    </select><br>
    <label for="edit_station_type">Select Waste Type:</label>
    <select name="edit_station_type">
        <?php
        $resultTypes = mysqli_query($conn, "SELECT * FROM TypeWaste");

        while ($rowType = mysqli_fetch_assoc($resultTypes)) {
            echo "<option value='{$rowType['TypeID']}'>{$rowType['Description']}</option>";
        }
        ?>
    </select><br>
    <input type="submit" value="Edit Measurement Station">
</form>
<!-- Delete Measurement Station Form -->
<h3>Delete Measurement Station</h3>
<form method="POST" action="admin_actions.php">
    <label for="delete_station_id">Select Station:</label>
    <select name="delete_station_id">
        <?php
        $resultStations = mysqli_query($conn, "SELECT * FROM MeasurementStation");

        while ($rowStation = mysqli_fetch_assoc($resultStations)) {
            echo "<option value='{$rowStation['StationID']}'>{$rowStation['Description']}</option>";
        }
        ?>
    </select><br>
    <input type="submit" value="Delete Measurement Station">
</form>

    </section>

  

    <footer>
        <p><b><i>©Loan Kuhlmann 2023</i></b></p>
    </footer>
</body>

</html>
