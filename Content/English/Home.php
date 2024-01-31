<?php include "../lang.php" ?>
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

        article {
            max-width: 1600px;
            height: 600px;
            margin: 20px auto;
            padding: 60px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 25px;
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

        section {
            max-width: 1600px; 
            margin: 40px auto; 
            padding: 40px; 
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
        }


        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 40px;
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
        <?php
        include '../Navbar.php';
        ?>
    </nav>

    <article>
        <h1>
            <b>Welcome</b>
        </h1>

        <section>
            <?php
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

            if (isset($_SESSION["username"])) {
                // User is logged in, get the user's favorite center
                $user = $_SESSION["username"];
                $result = mysqli_query($conn, "SELECT * FROM RecyclingUser WHERE UserName = '$user'");
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $centerID = $row['FavoriteCenter'];
                    
                    // Retrieve information for the user's favorite center
                    $resultCenter = mysqli_query($conn, "SELECT * FROM RecyclingCenter WHERE CenterID = $centerID");
                    
                    if ($rowCenter = mysqli_fetch_assoc($resultCenter)) {
                        $centerName = $rowCenter['CenterName'];
                        $address = $rowCenter['Address'];
                        $pc = $rowCenter['PC'];
                        $town = $rowCenter['Town'];
                        $openStart = $rowCenter['OpenStart'];
                        $openStop = $rowCenter['OpenStop'];

                        echo "<p><b>{$centerName}</b></p>";
                        echo "<p>{$address}, {$pc} {$town}</p>";
                        echo "<p>{$openStart} - {$openStop}</p>";
                        echo '<a href="user.php" class="button">Edit Favourite center</a>';
                    } else {
                        echo "<p>No favorite center set</p>";
                    }
                } else {
                    echo "<p>No favorite center set</p>";
                }
            } else {
                // User is not logged in, retrieve a random recycling center
                $result = mysqli_query($conn, "SELECT * FROM RecyclingCenter ORDER BY RAND() LIMIT 1");

                if ($row = mysqli_fetch_assoc($result)) {
                    $centerName = $row['CenterName'];
                    $address = $row['Address'];
                    $pc = $row['PC'];
                    $town = $row['Town'];
                    $openStart = $row['OpenStart'];
                    $openStop = $row['OpenStop'];

                    echo "<p><b>{$centerName}</b></p>";
                    echo "<p>{$address}, {$pc} {$town}</p>";
                    echo "<p>{$openStart} - {$openStop}</p>";
                } else {
                    echo "<p>No recycling centers available</p>";
                }
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </section>

    </article>

    <footer>
        <p><b><i>©Loan Kuhlmann 2023</i></b></p>
    </footer>

</body>

</html>
