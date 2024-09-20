<?php
// Example of hashing a password
$password = 'your_password';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Save $hashed_password to your database
?>
