<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->query("SELECT * FROM profile WHERE user_id =" . $_SESSION['user_id']);
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
};

?>
<html>

<head>
    <?php require_once 'head.php' ?>
    <title>Training Log Profile App</title>
</head>

<body>
    <div class='container'>
        <h1>Training Log Profile App</h1>

        <?php

        flashMessages();

        if (isset($_SESSION['name']) && isset($_SESSION['user_id'])) {
            echo "<p><a href='logout.php'>Logout</a></p>";
            echo "<p><a href='add.php'>Add New</a></p>";
            echo "<table border='1'>";
            echo "<tr><th>Name</th><th>Headline</th><th>Manage</th></tr>";
            foreach ($profiles as $profile) {
                echo "<tr><td><a href='view.php?profile_id=" . $profile['profile_id'] . "'>";
                echo htmlentities($profile['first_name']) . " " . htmlentities($profile['last_name']);
                echo "</a></td><td>";
                echo htmlentities($profile['headline']);
                echo "</td><td>";
                echo "<a href='edit.php?profile_id=" . $profile['profile_id'] . "'>Edit</a> / ";
                echo "<a href='delete.php?profile_id=" . $profile['profile_id'] . "'>Delete</a>";
                echo "</td></tr>";
            }
            echo "</table>";
        } else {
            $_SESSION['error'] = 'Please log in';
            unset($_SESSION['name']);
            unset($_SESSION['user_id']);
            header('Location: login.php');
            return;
        }
        ?>
    </div>
</body>

</html>