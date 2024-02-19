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

        article {
            max-width: 1600px;
            height: 800px;
            margin: 20px auto;
            padding: 60px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 25px;
        }

        section {
            max-width: 1600px; 
            margin: 40px auto; 
            padding: 40px; 
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
        .button {
    display: inline-block;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    background-color: #4caf50;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}

.button:hover {
    background-color: #45a049;
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

        <article>
            <h1>
                <b>Your Account</b>
            </h1>

            <section>
                <p><b>Username:</b> <?php echo $rowUser['UserName']; ?></p>
                <p><b>Name:</b> <?php echo $rowUser['Name']; ?></p>
                <p><b>Email:</b> <?php echo $rowUser['Email']; ?></p>

                <?php
                // Retrieve the favorite center name
                $favoriteCenterID = $rowUser['FavoriteCenter'];
                $resultCenter = mysqli_query($conn, "SELECT CenterName FROM RecyclingCenter WHERE CenterID = $favoriteCenterID");
                $rowCenter = mysqli_fetch_assoc($resultCenter);
                $favoriteCenterName = $rowCenter['CenterName'];
                ?>

                <p><b>Favorite Recycling Center:</b> <?php echo $favoriteCenterName; ?></p>
                <p><b>Random Code:</b> <?php echo $rowUser['RandomCode']; ?></p>
                <form method="post" action="Qrc.php" style="display: inline;">
                <button type="submit" class="button">Your QR code</button>
                </form>
                
    <button type="button" class="button" onclick="redirectToChangePassword()">Edit Account settings</button>
    <form method="post" action="invoices.php" style="display: inline;">
                <button type="submit" class="button">Your Invoices</button>
</form>
                <form method="post" action="history.php" style="display: inline;">
                <button type="submit" class="button">Deposit History</button>


                
</form>

<script>
    function redirectToChangePassword() {
        window.location.href = "change_password.php";
    }
</script>

 

        </article>


    <?php
} else {
    // Redirect to login page or handle the case when the user is not logged in
    header("Location: login.php");
    exit();
}
?>
</section>
        <footer>
            <p><b><i>©Loan Kuhlmann 2023</i></b></p>
        </footer>

    </body>

    </html>
