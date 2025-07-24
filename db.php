<?php
$host = "localhost";
$user = "root";
$password = "sathwik@123";  // leave empty for XAMPP
$dbname = "grampower_db";  // your database name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Optional: echo success if you want to check
// echo "✅ Connected successfully!";
?>
