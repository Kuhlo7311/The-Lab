<?php
include "./../lang.php";

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

// If the form is submitted, insert the user into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST["username"];
    $password = $_POST["password"];
    $retypePassword = $_POST["retype_password"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $userType = "user";

    // Check if the password and re-entered password match
    if ($password != $retypePassword) {
        die("Passwords do not match");
    }

    // Check if the email contains an "@" symbol
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Increase the password column size
    $stmt = $conn->prepare("ALTER TABLE RecyclingUser MODIFY COLUMN Password VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci");
    $stmt->execute();
    $stmt->close();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a random code
    $randomCode = generateRandomCode();

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO RecyclingUser (UserName, Password, Name, Email, UserType, FavoriteCenter, RandomCode) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $user, $hashedPassword, $name, $email, $userType, $_POST["favorite_center"], $randomCode);
    $stmt->execute();
    $stmt->close();

    // Redirect to login page
    header("Location: ./login.php");
    exit();
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

<body>
    <header>
        <img src="../Media/Recy.png" alt="logo" style="width: 100px; height: 100px; float: center">
    </header>

    <nav>
        <?php include '../Navbar.php'; ?>
    </nav>

    <section>
        <form method="post">
            <label>Username:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <label>Retype Password:</label><br>
            <input type="password" name="retype_password" required><br><br>

            <label>Name:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Favorite Recycling Center:</label><br>
            <select name="favorite_center">
                <?php
                // Retrieve recycling centers from the database
                $result = mysqli_query($conn, "SELECT * FROM RecyclingCenter");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['CenterID']}'>{$row['CenterName']}</option>";
                }
                ?>
            </select><br><br>

            <!-- User type is set to 'user' by default -->
            <input type="hidden" name="user_type" value="user">

            <input type="submit" value="Register">
        </form>
    </section>

    <footer>
        <p><b><i>©Loan Kuhlmann 2022</i></b></p>
    </footer>
</body>

</html>
