<?php
// PHP code for password change and user information update
include "./../lang.php";

// Set up database connection
$host = "localhost";
$user = "root";
$psw = "yqt";
$db = "RecyclingDB";
$conn = mysqli_connect($host, $user, $psw, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user's current favorite center
$currentUserFavoriteCenterID = 0; // Set a default value
if (isset($_SESSION["username"])) {
    $user = $_SESSION["username"];
    $resultUser = mysqli_query($conn, "SELECT * FROM RecyclingUser WHERE UserName = '$user'");
    if ($rowUser = mysqli_fetch_assoc($resultUser)) {
        $currentUserFavoriteCenterID = $rowUser['FavoriteCenter'];
    }
}

// Function to generate a random code
function generateRandomCode($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_+=';
    $randomCode = '';

    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomCode;
}

// Function to generate a new random code for the currently logged in user
function generateNewRandomCode() {
    global $conn;

    // Generate a new random code
    $newRandomCode = generateRandomCode();

    // Update the user's random code in the database
    $stmt = $conn->prepare("UPDATE RecyclingUser SET RandomCode = ? WHERE UserName = ?");
    $stmt->bind_param("ss", $newRandomCode, $_SESSION["username"]);
    $stmt->execute();
}

// If the form is submitted, check and update user credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_psw = $_POST["old_password"];
    $new_psw = $_POST["new_password"];
    $retype_psw = $_POST["retype_password"];
    $new_username = $_POST["new_username"];
    $new_email = $_POST["new_email"];
    $new_favorite_center = $_POST["new_favorite_center"];

    $alertMessages = array(); // Array to store alert messages

    // If the "Generate New Code" button is clicked
    if (isset($_POST["generate_new_code"])) {
        generateNewRandomCode();
        $alertMessages[] = "New random code generated!";
    }

    // Prepare SQL statement to check old password
    $stmt = $conn->prepare("SELECT * FROM RecyclingUser WHERE UserName = ?");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Verify the password
    if (password_verify($old_psw, $row['Password'])) {
        // Old password is correct
        if (!empty($new_psw)) {
            // New password is provided, update the password
            if ($new_psw == $retype_psw) {
                // New password and retype password match
                // Hash the new password
                $hashed_psw = password_hash($new_psw, PASSWORD_DEFAULT);

                // Update the password in the database
                $stmt = $conn->prepare("UPDATE RecyclingUser SET Password = ? WHERE UserName = ?");
                $stmt->bind_param("ss", $hashed_psw, $_SESSION["username"]);
                $stmt->execute();

                $alertMessages[] = "Password successfully changed!";
            } else {
                $alertMessages[] = "New password and retype password do not match.";
            }
        }

        // Update the username if provided
        if (!empty($new_username)) {
            $stmt = $conn->prepare("UPDATE RecyclingUser SET UserName = ? WHERE UserName = ?");
            $stmt->bind_param("ss", $new_username, $_SESSION["username"]);
            $stmt->execute();

            // Update the session username with the new username
            $_SESSION["username"] = $new_username;

            $alertMessages[] = "Username successfully changed!";
        }

        // Update the email if provided
        if (!empty($new_email)) {
            $stmt = $conn->prepare("UPDATE RecyclingUser SET Email = ? WHERE UserName = ?");
            $stmt->bind_param("ss", $new_email, $_SESSION["username"]);
            $stmt->execute();

            $alertMessages[] = "Email successfully changed!";
        }

        // Update the favorite center if provided and it's different from the current favorite center
        if ($new_favorite_center != $currentUserFavoriteCenterID) {
            $stmt = $conn->prepare("UPDATE RecyclingUser SET FavoriteCenter = ? WHERE UserName = ?");
            $stmt->bind_param("is", $new_favorite_center, $_SESSION["username"]);
            $stmt->execute();

            $alertMessages[] = "Favorite Recycling Center successfully changed!";
        }

    } else {
        // Authentication failed, show error message
        $alertMessages[] = "Invalid old password.";
    }

    // Combine alert messages into a single message
    $combinedAlertMessage = implode('\n', $alertMessages);

    // Display the combined alert message only if there are messages
    if (!empty($alertMessages)) {
        echo "<script>alert('$combinedAlertMessage');</script>";
    }
    if (!empty($alertMessages)) {
        echo '<div style="color: #ff0000;">';
        foreach ($alertMessages as $message) {
            echo "$message<br>";
        }
        echo '</div>';
    }
    
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

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

<header>
    <img src="../Media/Recy.png" alt="logo" style="width: 100px; height: 100px; float: center">
</header>

<body>
    <nav>
        <?php include '../Navbar.php'; ?>
    </nav>

    <section>
        <h2>Change Password and User Information</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="old_password">Current Password:</label>
            <input type="password" name="old_password" required><br>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password"><br>

            <label for="retype_password">Retype Password:</label>
            <input type="password" name="retype_password"><br>

            <label for="new_username">New Username:</label>
            <input type="text" name="new_username"><br>

            <label for="new_email">New Email:</label>
            <input type="email" name="new_email"><br>

            <label for="new_favorite_center">New Favorite Recycling Center:</label>
            <select name="new_favorite_center">
                <?php
                // Retrieve recycling centers from the database
                $resultCenters = mysqli_query($conn, "SELECT * FROM RecyclingCenter");

                while ($rowCenter = mysqli_fetch_assoc($resultCenters)) {
                    $selected = ($rowCenter['CenterID'] == $currentUserFavoriteCenterID) ? "selected" : "";
                    echo "<option value='{$rowCenter['CenterID']}' $selected>{$rowCenter['CenterName']}</option>";
                }
                ?>
            </select><br>

            <!-- Button to generate a new random code -->
            <input type="submit" name="generate_new_code" value="Generate New Code">

            <input type="submit" value="Submit">
        </form>
    </section>
</body>

<footer>
    <p><b><i>©Loan Kuhlmann 2023</i></b></p>
</footer>

</html>
