<?php
/**
 * Admin Account Creator/Verifier
 * Run this file once to create or reset admin account
 * Access: http://localhost/your_project/admin/create_admin.php
 * 
 * DELETE THIS FILE AFTER CREATING ADMIN ACCOUNT!
 */

include '../includes/config.php';

// Admin credentials
$username = 'admin';
$email = 'admin@mwanamama.com';
$password = 'admin123';
$full_name = 'System Administrator';
$role = 'super_admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check = $conn->query("SELECT * FROM admins WHERE username = '$username' OR email = '$email'");

if ($check->num_rows > 0) {
    // Update existing admin
    $sql = "UPDATE admins SET 
            password = '$hashed_password',
            status = 'active',
            role = '$role'
            WHERE username = '$username' OR email = '$email'";
    
    if ($conn->query($sql)) {
        echo "✅ <strong>Admin account updated successfully!</strong><br><br>";
    } else {
        echo "❌ <strong>Error updating admin:</strong> " . $conn->error . "<br><br>";
    }
} else {
    // Create new admin
    $sql = "INSERT INTO admins (username, email, password, full_name, role, status, created_at) 
            VALUES ('$username', '$email', '$hashed_password', '$full_name', '$role', 'active', NOW())";
    
    if ($conn->query($sql)) {
        echo "✅ <strong>Admin account created successfully!</strong><br><br>";
    } else {
        echo "❌ <strong>Error creating admin:</strong> " . $conn->error . "<br><br>";
    }
}

// Display credentials
echo "<div style='font-family: Arial; padding: 20px; background: #f0f0f0; border-radius: 10px; max-width: 600px;'>";
echo "<h2>🔐 Admin Login Credentials</h2>";
echo "<table style='width: 100%; background: white; padding: 15px; border-radius: 5px;'>";
echo "<tr><td><strong>Username:</strong></td><td>" . $username . "</td></tr>";
echo "<tr><td><strong>Email:</strong></td><td>" . $email . "</td></tr>";
echo "<tr><td><strong>Password:</strong></td><td>" . $password . "</td></tr>";
echo "<tr><td><strong>Hashed Password:</strong></td><td style='font-size: 10px; word-break: break-all;'>" . $hashed_password . "</td></tr>";
echo "</table>";
echo "<br>";
echo "<p><strong>⚠️ Important:</strong></p>";
echo "<ol>";
echo "<li>Test the login at: <a href='login.php'>login.php</a></li>";
echo "<li>Change the password after first login</li>";
echo "<li><strong>DELETE THIS FILE (create_admin.php) after creating the account!</strong></li>";
echo "</ol>";
echo "</div>";

// Test password verification
echo "<br><div style='font-family: Arial; padding: 20px; background: #e8f5e9; border-radius: 10px; max-width: 600px;'>";
echo "<h3>🧪 Password Verification Test</h3>";
if (password_verify($password, $hashed_password)) {
    echo "✅ Password verification: <strong>WORKING</strong><br>";
    echo "The password 'admin123' correctly verifies against the hashed password.";
} else {
    echo "❌ Password verification: <strong>FAILED</strong><br>";
    echo "Something is wrong with password hashing.";
}
echo "</div>";

// Check database connection
echo "<br><div style='font-family: Arial; padding: 20px; background: #e3f2fd; border-radius: 10px; max-width: 600px;'>";
echo "<h3>🔍 Database Status</h3>";
$admin_check = $conn->query("SELECT * FROM admins WHERE username = '$username'");
if ($admin_check && $admin_check->num_rows > 0) {
    $admin_data = $admin_check->fetch_assoc();
    echo "✅ Admin found in database<br>";
    echo "Username: " . $admin_data['username'] . "<br>";
    echo "Email: " . $admin_data['email'] . "<br>";
    echo "Status: " . $admin_data['status'] . "<br>";
    echo "Role: " . $admin_data['role'] . "<br>";
} else {
    echo "❌ Admin not found in database<br>";
}
echo "</div>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .button:hover {
            background: #45a049;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning">
            <strong>⚠️ SECURITY WARNING:</strong><br>
            This file creates/resets the admin account. <br>
            <strong>DELETE THIS FILE IMMEDIATELY</strong> after creating your admin account to prevent unauthorized access!
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="login.php" class="button">Go to Login Page</a>
        </div>
    </div>
</body>
</html>