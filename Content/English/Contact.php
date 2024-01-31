<?php include "./../lang.php" ?>
<!DOCTYPE html>
<html lang="<?=$lang?>">

<head>
<link rel="stylesheet" href="../style.scss" time(): int>
<style>    footer {
          
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }</style>
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

    <section>



        <article>
            <h1>
                <b><?=$lang['Our Contact Information']?>:</b>
            </h1>
            <h1>
                <b><i><?=$lang['nameCompany']?>:</i></b>
            </h1>
            <h1>

                Ecoweighing S.A </h1>
            <h1>
            <b><i><?=$lang['Address']?>:</i></b>
            </h1>
            <h1>
                <b>Stäreplatz Luxebourg ville</b>
                  
            </h1>
            <h1>
                <b>Tel: (+352) 691 123 456</b>
            </h1>
            <h1>
                <b>Email: Ecoweigh@eco.com</b>
            </h1>

           





        </article>
    </section>



</body>

<footer>
        <p><b><i>©Loan Kuhlmann 2024</i></b></p>
    </footer>
</html>