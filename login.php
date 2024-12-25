<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "web"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function loginUser($conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $sql = "SELECT * FROM users WHERE name = '$username'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $storedpass = $row['PASSWORD'];
           
            if ($result->num_rows > 0 &&password_verify($password, $storedpass) ) {
                $role = $row['role'];
                session_start();
                if ($role == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: userPage.php");
                }
             } else {
                 echo "Invalid username or password.";
            }
        } 
    }
}

loginUser($conn);

$conn->close();
?>