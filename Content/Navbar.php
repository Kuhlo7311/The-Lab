<?php
$ArrayOfNavbuttons = [
    "en" => ["Home", "Register", "Login", "Logout", "Profile"],
    "AdminLinks" => ["admin_actions"],
    "Links" => ["Home", "register", "login", "Logout", "User", "admin_actions"],
];

// Checks if the username and user type are set in the session
$isLoggedIn = isset($_SESSION["username"]);
$isAdmin = isset($_SESSION["userType"]) && $_SESSION["userType"] === "admin";

// Sets the array of buttons based on the user's session status and type
$buttonsToDisplay = $isLoggedIn ? array_diff($ArrayOfNavbuttons["en"], ["Register", "Login"]) : array_diff($ArrayOfNavbuttons["en"], ["Logout", "Profile"]);

foreach ($buttonsToDisplay as $key => $value) {
    ?>
    <li>
        <div id="nav_bar">
            <a href="<?= $ArrayOfNavbuttons['Links'][$key] . '.php?lang=' . $_SESSION['lang'] ?>" class="<?= $key == $activePage ? "highlight" : "" ?>"><?= $value ?></a>
        </div>
    </li>
<?php
}

// Check separately for the "Admin page" button and if the user is an admin
if ($isLoggedIn && $isAdmin) {
    ?>
    <li>
        <div id="nav_bar">
            <a href="<?= $ArrayOfNavbuttons['AdminLinks'][0] . '.php?lang=' . $_SESSION['lang'] ?>" class="<?= $activePage == "admin_actions" ? "highlight" : "" ?>">Admin page</a>
        </div>
    </li>
<?php
}

// Displays additional information for logged-in users
if ($isLoggedIn) {
    ?>
    <div id="li_a" style="color: white;">
        <li>Logged in as <?= $_SESSION["username"] ?></li>
    </div>
<?php
}
?>
