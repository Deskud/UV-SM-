<?php
require "dbconnection.php";

session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conne, $sql);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['username'] = $username;
            $_SESSION['access_id'] = $row['access_id']; // Store the user's role
            $_SESSION['user_id'] = $row['user_id']; 
            
            header("Location: index.php");
            exit();
        } else {
            echo '<script>alert("These credentials do not match our records."); window.location.href = "login.html"</script>';
        }
    } else {
        echo '<script>alert("These credentials do not match our records."); window.location.href = "login.html"</script>';
    }
}

?>