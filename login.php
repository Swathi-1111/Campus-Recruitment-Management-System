<?php
session_start();
include 'db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='$role'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['role'] = $role;
    if ($role == 'admin') {
        header('Location: admin_dashboard.php');
    } elseif ($role == 'student') {
        header('Location: student_dashboard.php');
    } elseif ($role == 'company') {
        header('Location: company_dashboard.php');
    }
} else {
    echo 'Invalid credentials.';
}
?>