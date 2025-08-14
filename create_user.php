<?php
$password = "28772"; // İstediğin şifre
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
?>
