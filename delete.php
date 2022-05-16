<?php
require_once 'pdo.php';
session_start();

if (!isset($_SESSION['name']) && !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in';
    header('Location: index.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id = :pid;
                            DELETE FROM position WHERE profile_id = :pid');
    $stmt->execute(array(
        ':pid' => $_POST['profile_id']
    ));
    $_SESSION['success'] = 'Profile deleted';
    header('Location: index.php');
    return;
};

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = 'Missing User ID';
    header('Location: index.php');
    return;
};

$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :uid');
$stmt->execute(array(
    ':uid' => $_GET['profile_id']
));

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SESSION['user_id'] !== $row['user_id']) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: index.php');
    return;
}

if ($row === false) {
    $_SESSION['error'] = 'Invalid User ID';
    header('Location: index.php');
    return;
};

?>


<html>

<head>
    <?php require_once 'head.php' ?>
    <title>Delete Profile</title>
</head>

<body>
    <div class='container'>
        <h1>Delete Profile</h1>
        <p>Are you sure you want to delete profile for <?= htmlentities($row['first_name']) ?>?</p>
        <form method='post'>
            <p>
                <input type='hidden' name='profile_id' value='<?= htmlentities($row['profile_id']) ?>'>
                <input type='submit' name='delete' value='Delete'>
                <a href='index.php'>Cancel</a>
            </p>
        </form>
    </div>
</body>

</html>