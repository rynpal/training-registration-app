<?php
$pdo = new PDO(getenv('MYSQLCONNSTR'));
$pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
?>
