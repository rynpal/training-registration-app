<?php
$pdo = new PDO(getenv('MYSQLCONNSTR_TRAININGAPP'));
$pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
?>