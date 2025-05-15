<?php
session_start();
include('../includes/dbconn.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT username, password FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    body.login-bg {
    background: url('images/background.jpg') no-repeat center center fixed;
    background-size: cover;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>

<body class="login-bg">
    <div class="login-container">
        <h2>Hostel Management System</h2>
        <form method="POST" action="">
            <label>Your Username or Email</label>
            <input type="text" name="username" placeholder="admin" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>