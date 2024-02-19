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
            <b>Qr Code</b>       
        </h1>

        <section>
 <img src="Qrcode.php" alt="Qr code">
</section>
<?php  if (extension_loaded('gd')) {
    echo "QR Generation status: online";
} else {
    echo "QR Generation status: offline";
}
?>
    </article>

  
    <footer>
        <p><b><i>©Loan Kuhlmann 2024</i></b></p>
    </footer>

</body>

</html>
