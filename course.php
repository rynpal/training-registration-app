<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['name']) && !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in';
    header('Location: index.php');
    return;
}

if (isset($_GET['term'])) {
    $stmt = $pdo->prepare('SELECT name FROM course WHERE name LIKE :term');

    $stmt->execute(array(':term' => "%" . $_REQUEST['term'] . "%"));

    $retval = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $retval[] = $row['name'];
    }

    echo json_encode($retval, JSON_PRETTY_PRINT);
}
