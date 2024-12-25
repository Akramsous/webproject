<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "web"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function registerUser($conn) {
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $phone = $_POST['phone'];
        $role = $_POST['role'];
        $uploadDir = 'uploads/';

        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $profilePhotoName = $_FILES['profile_photo']['name'];
        $profilePhotoTmpName = $_FILES['profile_photo']['tmp_name'];
        $profilePhotoPath = $uploadDir . uniqid() . '_' . basename($profilePhotoName);
        move_uploaded_file($profilePhotoTmpName, $profilePhotoPath);
         }

    
    if (isset($_FILES['additional_file']) && $_FILES['additional_file']['error'] == 0) {
        $additionalFileName = $_FILES['additional_file']['name'];
        $additionalFileTmpName = $_FILES['additional_file']['tmp_name'];
        $additionalFilePath = $uploadDir . uniqid() . '_' . basename($additionalFileName);
        move_uploaded_file($additionalFileTmpName, $additionalFilePath);
    }


        
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role, profile_photo, additional_file) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $username, $email, $password, $phone, $role, $profilePhotoPath, $additionalFilePath);

if ($stmt->execute()) {
header("Location: index.php");
exit();
} else {
echo "Error: " . $stmt->error;
}
$stmt->close();
}
}


registerUser($conn);

$conn->close();
?>