<?php
/**
 * Session Fix Utility
 * This file checks and fixes session configuration issues
 * Run this once if you have session problems
 */

// Check current session configuration
echo "<h2>Current Session Configuration</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><td><strong>Session Status</strong></td><td>" . (session_status() === PHP_SESSION_ACTIVE ? '✅ Active' : '❌ Not Active') . "</td></tr>";
echo "<tr><td><strong>Session Save Path</strong></td><td>" . session_save_path() . "</td></tr>";
echo "<tr><td><strong>Session Name</strong></td><td>" . session_name() . "</td></tr>";
echo "<tr><td><strong>Session ID</strong></td><td>" . (session_id() ?: 'None') . "</td></tr>";
echo "</table>";

// Check if session save path exists and is writable
$save_path = session_save_path();
echo "<h2>Session Directory Status</h2>";
echo "<table border='1' cellpadding='10'>";

if (empty($save_path)) {
    echo "<tr><td>❌ Session save path is empty</td></tr>";
    $save_path = sys_get_temp_dir();
    echo "<tr><td>Using system temp directory: $save_path</td></tr>";
}

if (file_exists($save_path)) {
    echo "<tr><td>✅ Directory exists: $save_path</td></tr>";
} else {
    echo "<tr><td>❌ Directory does NOT exist: $save_path</td></tr>";
    echo "<tr><td>Attempting to create directory...</td></tr>";
    if (@mkdir($save_path, 0777, true)) {
        echo "<tr><td>✅ Directory created successfully</td></tr>";
    } else {
        echo "<tr><td>❌ Failed to create directory</td></tr>";
    }
}

if (is_writable($save_path)) {
    echo "<tr><td>✅ Directory is writable</td></tr>";
} else {
    echo "<tr><td>❌ Directory is NOT writable</td></tr>";
    echo "<tr><td>Run this command: chmod 777 $save_path</td></tr>";
}

echo "</table>";

// Start a test session
echo "<h2>Testing Session Functionality</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['test'] = 'Session is working!';

echo "<table border='1' cellpadding='10'>";
if (isset($_SESSION['test'])) {
    echo "<tr><td>✅ Session write test PASSED</td></tr>";
    echo "<tr><td>Test value: " . $_SESSION['test'] . "</td></tr>";
    unset($_SESSION['test']);
} else {
    echo "<tr><td>❌ Session write test FAILED</td></tr>";
}
echo "</table>";

// Recommended fixes
echo "<h2>💡 Recommended Fixes</h2>";
echo "<ol>";
echo "<li><strong>Fix 1:</strong> Make sure the session directory exists and is writable:<br>";
echo "<code>sudo mkdir -p /opt/lampp/temp/</code><br>";
echo "<code>sudo chmod 777 /opt/lampp/temp/</code></li>";

echo "<li><strong>Fix 2:</strong> Update php.ini session.save_path:<br>";
echo "Location: /opt/lampp/etc/php.ini<br>";
echo "Find: <code>session.save_path</code><br>";
echo "Set to: <code>session.save_path = \"/opt/lampp/temp/\"</code></li>";

echo "<li><strong>Fix 3:</strong> Restart XAMPP:<br>";
echo "<code>sudo /opt/lampp/lampp restart</code></li>";

echo "<li><strong>Fix 4:</strong> Use alternative session path in config.php:<br>";
echo "<pre>";
echo "// Add this to config.php before session_start()\n";
echo "ini_set('session.save_path', '/tmp');\n";
echo "// or\n";
echo "ini_set('session.save_path', sys_get_temp_dir());";
echo "</pre></li>";

echo "<li><strong>Fix 5:</strong> Clear browser cookies and cache, then try logging in again</li>";
echo "</ol>";

// Quick fix button
echo "<h2>🔧 Quick Fix</h2>";
echo "<p>Click the button below to apply automatic fixes:</p>";

if (isset($_POST['apply_fix'])) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    
    // Try to create session directory
    if (!file_exists($save_path)) {
        if (@mkdir($save_path, 0777, true)) {
            echo "✅ Created session directory<br>";
        }
    }
    
    // Try to make it writable
    if (@chmod($save_path, 0777)) {
        echo "✅ Set directory permissions<br>";
    }
    
    echo "✅ Fixes applied! Now <a href='test_login.php'>test your login</a>";
    echo "</div>";
}

echo "<form method='POST'>";
echo "<button type='submit' name='apply_fix' style='padding: 15px 30px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>Apply Quick Fix</button>";
echo "</form>";

echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffc107; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>⚠️ Note:</strong> Some fixes may require server administrator privileges. ";
echo "If automatic fixes don't work, contact your server administrator or use Fix 4 above.";
echo "</div>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Fix Utility</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            background: white;
            margin: 10px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔧 Session Fix Utility</h1>
    <p style="background: white; padding: 15px; border-left: 4px solid #667eea;">
        This tool helps diagnose and fix session-related issues that prevent login from working.
    </p>
</body>
</html>