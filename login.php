<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

if (isset($_POST['email']) && isset($_POST['password'])) {

    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = 'Invalid Email';
        header('Location: login.php');
        return;
    }

    if (strlen($_POST['password']) < 1) {
        $_SESSION['error'] = 'Missing Password';
        header('Location: login.php');
        return;
    }

    $salt = 'XyZzy12*_';
    $check = hash('md5', $salt . $_POST['password']);



    $stmt = $pdo->prepare('SELECT user_id,name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':pw' => $check
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        $_SESSION['error'] = 'Incorrect Password';
        header('Location: login.php');
        return;
    }

    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header('Location: index.php');
        return;
    }
}


?>

<html>

<head>
    <?php require_once 'head.php' ?>
    <title>Training Log Profile Login Page</title>
</head>

<body>
    <div class="container">
        <h1>Training Log Profile App Login</h1>
        <?php
        flashMessages();
        ?>
        <form method='post'>
            <p>Email:
                <input type='text' name='email' id='email' />
            </p>
            <p>Password:
                <input type='password' name='password' id='pw' />
            </p>
            <p><input type='submit' value='Log In' onclick="validate()" /></p>
        </form>

        <script src='script.js'></script>
    </div>
</body>

</html>