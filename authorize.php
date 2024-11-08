<?php

function authorizeUserWithRole($user_id, $requiredRole) {
    // Database connection parameters
    $host = "localhost";
    $user = "root";
    $db_password = "";
    $dbname = "your_database";

    // Create a new connection
    $conn = mysqli_connect($host, $user, $db_password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare SQL to get role for the user based on user_id
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);  // Assuming user_id is an integer
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userRole);
        $stmt->fetch();

        // Check if the user's role matches the required role
        if ($userRole === $requiredRole) {
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $userRole;
            
            // Close the statement and connection
            $stmt->close();
            $conn->close();
            
            return true;  // User is authorized with the required role
        } else {
            $stmt->close();
            $conn->close();
            
            return false;  // Role mismatch
        }
    } else {
        $stmt->close();
        $conn->close();
        
        return false;  // User does not exist
    }
}
