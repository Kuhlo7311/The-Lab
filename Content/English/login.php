<?php

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

// Initialize login_error variable
$login_error = "";

// If the form is submitted, check login credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the key "identifier" is set in the $_POST array
    $login_identifier = isset($_POST["identifier"]) ? $_POST["identifier"] : "";
    $login_password = $_POST["password"];

    // Prepare SQL statement to check both username and email
    $stmt = $conn->prepare("SELECT UserID, UserName, Password, UserType FROM RecyclingUser WHERE UserName = ? OR Email = ?");
    $stmt->bind_param("ss", $login_identifier, $login_identifier);
    $stmt->execute();
    $stmt->store_result();

    // Check if the username or email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($UserID, $stored_user, $stored_password, $user_type);
        $stmt->fetch();

        // Verify the password
        $password_verified = password_verify($login_password, $stored_password);

        if ($password_verified) {
            // Password is correct, set session and redirect based on user type
            session_start();
            $_SESSION["username"] = $stored_user;
            $_SESSION["UserID"] = $UserID; // Store the UserID in the session
            $_SESSION["userType"] = $user_type;
            $_SESSION["logged"] = true;

            if ($user_type === "admin") {
                header("Location: admin_actions.php");
                exit();
            } else {
                header("Location: Home.php");
                exit();
            }
        } else {
            $login_error = "Incorrect password";
        }
    } else {
        $login_error = "Username or email not found";
    }

    // Close statement
    $stmt->close();
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
    <script>
        function validateForm() {
            var identifier = document.forms["loginForm"]["identifier"].value;
            var password = document.forms["loginForm"]["password"].value;

            if (identifier == "" || password == "") {
                alert("Both username/email and password must be filled out");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <header>
        <img src="../Media/Recy.png" alt="logo" style="width: 100px; height: 100px; float: center">
    </header>

    <nav>
        <?php include '../Navbar.php'; ?>
    </nav>

    <section>
        <form method="post">
            <label>Username or Email:</label><br>
            <input type="text" name="identifier" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <input type="submit" value="Login">
            <?php
                // Display login error message, if any
                if (!empty($login_error)) {
                    echo "<div style='color: red;'>$login_error</div>";
                }
            ?>
        </form>
    </section>

    <footer>
        <p><b><i>©Loan Kuhlmann 2023</i></b></p>
    </footer>

</body>

</html>
