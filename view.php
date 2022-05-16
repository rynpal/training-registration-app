<?php
require_once 'pdo.php';
session_start();

if (!isset($_SESSION['name']) && !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in';
    header('Location: index.php');
    return;
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = 'Missing profile id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :pid');
$stmt->execute(array(
    ':pid' => $_GET['profile_id']
));

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SESSION['user_id'] !== $row['user_id']) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: index.php');
    return;
}

if (!$row) {
    $_SESSION['error'] = 'Invalid profile id';
    header('Location: index.php');
    return;
};

$stmts = $pdo->prepare('SELECT * FROM position WHERE profile_id = :pid');
$stmts->execute(array(
    ':pid' => $_GET['profile_id']
));

$pos = $stmts->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT training.year, course.name FROM course INNER JOIN training ON course.course_id = training.course_id WHERE training.profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));

$course  = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once 'head.php' ?>
    <title>View Profile</title>
</head>
<div class='container'>

    <body>
        <h1>View Profile</h1>
        <p>Name: <?= htmlentities($row['first_name']) . " " . htmlentities($row['last_name']) ?></p>
        <p>Email: <?= htmlentities($row['email']) ?></p>
        <p>Headline:<br>
            <?= htmlentities($row['headline']) ?></p>
        <p>Summary:<br>
            <?= htmlentities($row['summary']) ?></p>
        <ul><b>Training Log:</b>
            <?php
            if (!$course) {
                echo '<p>No training entries</p>';
            }
            foreach ($course as $c) {
                echo '<li>' . htmlentities($c['year']) . ': ' . htmlentities($c['name']) . '</li>';
            }
            ?>
        </ul>
        <p></p>
        <ul><b>Note Log:</b>
            <?php
            if (!$pos) {
                echo '<p>No note entries</p>';
            }
            foreach ($pos as $p) {
                echo '<li>' . htmlentities($p['year']) . ': ' . htmlentities($p['description']) . '</li>';
            }
            ?>
        </ul>
        <a href='index.php'>Done</a>
</div>
</body>

</html>