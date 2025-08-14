<?php
// DB bilgileri
$host = 'localhost';
$dbname = 'crm_db';  // senin veritabanın
$user = 'root';      // XAMPP default
$pass = '';          // XAMPP default

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("DB Hatası: " . $e->getMessage());
}
