<?php

function check ($conn,$username,$email){
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
            echo "<script>alert('Error: Both username and email are already taken.'); window.location.href='index.php';</script>";
        } elseif ($UsernameTaken) {
            echo "<script>alert('Error: Username is already taken.'); window.location.href='index.php';</script>";
        } elseif ($EmailTaken) {
            echo "<script>alert('Error: Email is already taken.'); window.location.href='index.php';</script>";
        }
        exit();
    }
    
    }



        ?>