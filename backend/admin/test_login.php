<?php
/**
 * Login Diagnostic Tool
 * Use this to test your login credentials and troubleshoot issues
 * DELETE THIS FILE after fixing login issues!
 */

include '../includes/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Diagnostic Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #667eea;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .info {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table td:first-child {
            font-weight: bold;
            width: 200px;
        }
        .form-test {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        input {
            padding: 10px;
            width: 300px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Login Diagnostic Tool</h1>

        <!-- Database Connection Test -->
        <div class="test-section">
            <h2>1. Database Connection</h2>
            <?php
            if ($conn->connect_error) {
                echo '<p class="error">❌ Connection Failed: ' . $conn->connect_error . '</p>';
            } else {
                echo '<p class="success">✅ Database Connected Successfully</p>';
                echo '<p class="info">Server: ' . DB_HOST . ' | Database: ' . DB_NAME . '</p>';
            }
            ?>
        </div>

        <!-- Check Admins Table -->
        <div class="test-section">
            <h2>2. Admins Table Check</h2>
            <?php
            $check_table = $conn->query("SHOW TABLES LIKE 'admins'");
            if ($check_table && $check_table->num_rows > 0) {
                echo '<p class="success">✅ Admins table exists</p>';
                
                // Count admins
                $count_result = $conn->query("SELECT COUNT(*) as total FROM admins");
                $count = $count_result->fetch_assoc()['total'];
                echo '<p class="info">Total admin accounts: ' . $count . '</p>';
                
                // List all admins
                echo '<h3>Admin Accounts:</h3>';
                $admins = $conn->query("SELECT id, username, email, role, status FROM admins");
                if ($admins && $admins->num_rows > 0) {
                    echo '<table>';
                    echo '<tr><td><strong>Username</strong></td><td><strong>Email</strong></td><td><strong>Role</strong></td><td><strong>Status</strong></td></tr>';
                    while ($admin = $admins->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($admin['username']) . '</td>';
                        echo '<td>' . htmlspecialchars($admin['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($admin['role']) . '</td>';
                        echo '<td>' . htmlspecialchars($admin['status']) . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p class="error">❌ No admin accounts found!</p>';
                }
            } else {
                echo '<p class="error">❌ Admins table does not exist!</p>';
            }
            ?>
        </div>

        <!-- Test Password -->
        <div class="test-section">
            <h2>3. Password Test</h2>
            <?php
            $result = $conn->query("SELECT username, password FROM admins WHERE username = 'admin'");
            if ($result && $result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                $stored_hash = $admin['password'];
                $test_password = 'admin123';
                
                echo '<p>Testing password: <strong>' . $test_password . '</strong></p>';
                echo '<p class="info">Stored hash: ' . substr($stored_hash, 0, 50) . '...</p>';
                
                if (password_verify($test_password, $stored_hash)) {
                    echo '<p class="success">✅ Password verification SUCCESSFUL</p>';
                    echo '<p class="info">The password "admin123" works correctly!</p>';
                } else {
                    echo '<p class="error">❌ Password verification FAILED</p>';
                    echo '<p>The stored password hash may be incorrect.</p>';
                    
                    // Generate correct hash
                    $correct_hash = password_hash($test_password, PASSWORD_DEFAULT);
                    echo '<p class="info">Correct hash for "admin123": ' . $correct_hash . '</p>';
                    
                    // Update with correct hash
                    echo '<p><a href="create_admin.php" style="color: #667eea;">Click here to recreate admin account with correct password</a></p>';
                }
            } else {
                echo '<p class="error">❌ Admin user not found in database</p>';
            }
            ?>
        </div>

        <!-- Manual Login Test -->
        <div class="test-section">
            <h2>4. Manual Login Test</h2>
            <div class="form-test">
                <form method="POST" action="">
                    <label>Username or Email:</label><br>
                    <input type="text" name="test_username" value="admin" required><br><br>
                    
                    <label>Password:</label><br>
                    <input type="password" name="test_password" value="admin123" required><br><br>
                    
                    <button type="submit" name="test_login">Test Login</button>
                </form>
                
                <?php
                if (isset($_POST['test_login'])) {
                    $test_user = $conn->real_escape_string(trim($_POST['test_username']));
                    $test_pass = trim($_POST['test_password']);
                    
                    echo '<hr><h3>Test Results:</h3>';
                    
                    $sql = "SELECT * FROM admins WHERE (username = '$test_user' OR email = '$test_user') AND status = 'active'";
                    echo '<p class="info">Query: ' . $sql . '</p>';
                    
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        echo '<p class="success">✅ User found in database</p>';
                        $admin = $result->fetch_assoc();
                        
                        echo '<table>';
                        echo '<tr><td>Username:</td><td>' . htmlspecialchars($admin['username']) . '</td></tr>';
                        echo '<tr><td>Email:</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
                        echo '<tr><td>Status:</td><td>' . htmlspecialchars($admin['status']) . '</td></tr>';
                        echo '</table>';
                        
                        if (password_verify($test_pass, $admin['password'])) {
                            echo '<p class="success">✅ PASSWORD CORRECT - Login should work!</p>';
                            echo '<p><a href="login.php" style="color: #667eea; font-weight: bold;">Go to Login Page</a></p>';
                        } else {
                            echo '<p class="error">❌ PASSWORD INCORRECT</p>';
                            echo '<p>The password you entered does not match the stored hash.</p>';
                        }
                    } else {
                        echo '<p class="error">❌ User not found or account is inactive</p>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Session Test -->
        <div class="test-section">
            <h2>5. Session Configuration</h2>
            <?php
            if (session_status() === PHP_SESSION_ACTIVE) {
                echo '<p class="success">✅ Sessions are working</p>';
            } else {
                echo '<p class="error">❌ Sessions are not working</p>';
            }
            echo '<p class="info">Session Save Path: ' . session_save_path() . '</p>';
            ?>
        </div>

        <!-- Recommendations -->
        <div class="test-section">
            <h2>💡 Troubleshooting Steps</h2>
            <ol>
                <li>If admin user not found: <a href="create_admin.php">Create Admin Account</a></li>
                <li>If password verification fails: Run create_admin.php to reset password</li>
                <li>Clear your browser cookies and cache</li>
                <li>Make sure config.php has correct database credentials</li>
                <li>Check that sessions are enabled in php.ini</li>
                <li><strong>DELETE this file (test_login.php) after fixing issues!</strong></li>
            </ol>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>⚠️ SECURITY WARNING:</strong><br>
            This diagnostic file exposes sensitive information. <br>
            <strong>DELETE THIS FILE</strong> after resolving login issues!
        </div>
    </div>
</body>
</html>