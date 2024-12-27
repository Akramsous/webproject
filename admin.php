 
 <?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "web"; 

$conn = new mysqli($servername, $username, $password, $dbname);

session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add user
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $uploadDir = 'uploads/';

    // photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $profilePhotoName = $_FILES['profile_photo']['name'];
        $profilePhotoTmpName = $_FILES['profile_photo']['tmp_name'];
        $profilePhotoPath = $uploadDir . uniqid() . '_' . basename($profilePhotoName);
        move_uploaded_file($profilePhotoTmpName, $profilePhotoPath);
    }

    //  file upload
    if (isset($_FILES['additional_file']) && $_FILES['additional_file']['error'] == 0) {
        $additionalFileName = $_FILES['additional_file']['name'];
        $additionalFileTmpName = $_FILES['additional_file']['tmp_name'];
        $additionalFilePath = $uploadDir . uniqid() . '_' . basename($additionalFileName);
        move_uploaded_file($additionalFileTmpName, $additionalFilePath);
    }
    $checkemail_name = $conn->prepare("SELECT name, email FROM users WHERE name = ? OR email = ?");
    $checkemail_name->bind_param("ss", $username, $email);
    $checkemail_name->execute();
    $checkemail_name->bind_result($Nameexist, $Emailexist);
    
    $UsernameTaken = false;
    $EmailTaken = false;

    while ($checkemail_name->fetch()) {
        if ($Nameexist === $username) {
            $UsernameTaken = true;
        }
        if ($Emailexist === $email) {
            $EmailTaken = true;
        }
    }

    $checkemail_name->close();

    if ($UsernameTaken || $EmailTaken) {
        if ($UsernameTaken && $EmailTaken) {
            echo "<script>alert('Error: Both username and email are already taken.'); window.location.href='admin.php';</script>";
        } elseif ($UsernameTaken) {
            echo "<script>alert('Error: Username is already taken.'); window.location.href='admin.php';</script>";
        } elseif ($EmailTaken) {
            echo "<script>alert('Error: Email is already taken.'); window.location.href='admin.php';</script>";
        }
        exit();
    }
    // Insert the data into the database using prepared statements 
    $stmt = $conn->prepare("INSERT INTO users (name, email, PASSWORD, phone, role, profile_photo, additional_file) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $email, $password, $phone, $role, $profilePhotoPath, $additionalFilePath);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

//  Update (Edit User)

if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id']; 
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $uploadDir = 'uploads/';

    //  photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $profilePhotoName = $_FILES['profile_photo']['name'];
        $profilePhotoTmpName = $_FILES['profile_photo']['tmp_name'];
        $profilePhotoPath = $uploadDir . uniqid() . '_' . basename($profilePhotoName);
        move_uploaded_file($profilePhotoTmpName, $profilePhotoPath);
    }

    // file upload
    if (isset($_FILES['additional_file']) && $_FILES['additional_file']['error'] == 0) {
        $additionalFileName = $_FILES['additional_file']['name'];
        $additionalFileTmpName = $_FILES['additional_file']['tmp_name'];
        $additionalFilePath = $uploadDir . uniqid() . '_' . basename($additionalFileName);
        move_uploaded_file($additionalFileTmpName, $additionalFilePath);
    }
    $checkemail_name = $conn->prepare("SELECT name, email FROM users WHERE name = ? OR email = ?");
    $checkemail_name->bind_param("ss", $username, $email);
    $checkemail_name->execute();
    $checkemail_name->bind_result($Nameexist, $Emailexist);
    
    $UsernameTaken = false;
    $EmailTaken = false;

    while ($checkemail_name->fetch()) {
        if ($Nameexist === $username) {
            $UsernameTaken = true;
        }
        if ($Emailexist === $email) {
            $EmailTaken = true;
        }
    }

    $checkemail_name->close();

    if ($UsernameTaken || $EmailTaken) {
        if ($UsernameTaken && $EmailTaken) {
            echo "<script>alert('Error: Both username and email are already taken.'); window.location.href='admin.php';</script>";
        } elseif ($UsernameTaken) {
            echo "<script>alert('Error: Username is already taken.'); window.location.href='admin.php';</script>";
        } elseif ($EmailTaken) {
            echo "<script>alert('Error: Email is already taken.'); window.location.href='admin.php';</script>";
        }
        exit();
    }
    
    $sql ="UPDATE users SET name ='$username',email='$email',PASSWORD='$password',phone='$phone',role='$role',profile_photo='$profilePhotoPath', additional_file='$additionalFilePath' WHERE user_id='$user_id'";
    // Update the data in the database 
    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}


//  Delete 
if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];
    // Delete the user from the database 
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        if($user_id == $_SESSION['user_id']){
        session_destroy();
        header("Location: index.php");
    }
        echo "User deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch users to display in the table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error fetching users: " . $conn->error);
}

?> 

 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page - Hotel Management System</title>
    <link rel="stylesheet" href="admin.css" />
</head>

<body>

<nav class="navbar">
    <p>Hotel Management System - Admin Page</p>
    <div>
        <a href="index.php">Log Out</a>
    </div>
</nav>
<main>
    
    <div class="add-user-container">
        <button id="addUserButton" class="click" onclick="toggleAddUserForm()">Add User</button>
    </div>

    
    <div id="addUserForm" class="user-form" style="display: none;">
        <h3>Add New User</h3>
        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <div class="form-field">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-field">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-field">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-field">
                <input type="tel" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
            </div>
            <div class="form-field">
                <label for="role">Role:</label>
                <select name="role" id="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-field">
                <input type="file" name="profile_photo" accept="image/*" id="profilePhoto" required>
            </div>
            <div class="form-field">
                <input type="file" name="additional_file" accept=".pdf,.doc,.docx,.txt" id="additionalFile" required>
            </div>
            <div class="form-field">
                <button type="submit" name="add_user" class ="click">Add User</button>
                <button type="button" class ="click" onclick="toggleAddUserForm()">Cancel</button>
            </div>
        </form>
    </div>

    
    <h2 class="infotable">Users Information</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Role</th>
            <th>Profile Photo</th>
            <th>Additional File</th>
        </tr>
        </thead>
        <tbody>
         <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td><img src="<?php echo $row['profile_photo']; ?>" alt="Profile Photo" width="100px" height="100px"></td>
                <td><a href="<?php echo $row['additional_file']; ?>" download> <?php echo $row['additional_file']; ?></a></td>
                <td>
                    <a href="admin.php?delete_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" style="text-decoration: none">
                        <button class = "click" >Delete</button>
                    </a>
                    <a href ="#editUserForm"><button class ="click" onclick="editUser(
                        <?php echo $row['user_id']; ?>,
                        '<?php echo addslashes($row['name']); ?>',
                        '<?php echo addslashes($row['email']); ?>',
                        '<?php echo addslashes($row['phone']); ?>',
                        '<?php echo addslashes($row['role']); ?>',
                        '<?php echo addslashes($row['PASSWORD']); ?>'
                    )">Edit</button></a>
                </td>
            </tr>
        <?php } ?> 
        </tbody>
    </table>
    <div id="editUserForm" class="user-form" style="display: none;">
        <h3>Edit User</h3>
        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" id="editUserId">
            <div class="form-field">
                <input type="text" name="username" id="editUsername" placeholder="Username" required>
            </div>
            <div class="form-field">
                <input type="email" name="email" id="editEmail" placeholder="Email" required>
            </div>
            <div class="form-field">
                <input type="password" name="password" id="editPassword" placeholder="Password">
            </div>
            <div class="form-field">
                <input type="tel" name="phone" id="editPhone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
            </div>
            <div class="form-field">
            <input type="file" name="profile_photo" accept="image/*" id="profilePhoto" placeholder="Image" required>
            </div>
            <div class="form-field">
            <input type="file" name="additional_file" accept=".pdf,.doc,.docx,.txt" id="additionalFile" placeholder="File" required>
            </div>
            <div class="form-field">
                <label for="role">Role:</label>
                <select name="role" id="editRole">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-field">
                <button type="submit" name="edit_user" class ="click">Save Changes</button>
                <button type="button" class ="click" onclick="toggleEditUserForm()">Cancel</button>
            </div>
        </form>
    </div>

</main>

<script src="admin.js"></script>
</body>

</html>

 <?php
$conn->close();
?> 
