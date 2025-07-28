<?php
// Database Configuration
$host = 'localhost';      // Change this if your server uses a different host
$dbname = 'campus_recruitment'; // Replace with your database name
$username = 'root';       // Replace with your MySQL username
$password = '';           // Replace with your MySQL password

// Create Connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Connection Success
// Uncomment below line if you'd like confirmation
// echo 'Database connection successful!';
?>