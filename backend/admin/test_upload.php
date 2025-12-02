<?php
/**
 * Upload Diagnostic Tool
 * Tests file upload functionality
 */

include '../includes/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td { padding: 8px; border-bottom: 1px solid #ddd; }
        table td:first-child { font-weight: bold; width: 250px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Upload Diagnostic Tool</h1>

        <h2>1. Upload Directory Check</h2>
        <?php
        $upload_dir = "../uploads/";
        echo "<table>";
        
        // Check if directory exists
        if (file_exists($upload_dir)) {
            echo "<tr><td>Directory Exists</td><td class='success'>✅ YES</td></tr>";
        } else {
            echo "<tr><td>Directory Exists</td><td class='error'>❌ NO</td></tr>";
            echo "<tr><td colspan='2'>Attempting to create...</td></tr>";
            if (mkdir($upload_dir, 0777, true)) {
                echo "<tr><td colspan='2' class='success'>✅ Directory created!</td></tr>";
            } else {
                echo "<tr><td colspan='2' class='error'>❌ Failed to create directory</td></tr>";
            }
        }
        
        // Check if writable
        if (is_writable($upload_dir)) {
            echo "<tr><td>Directory Writable</td><td class='success'>✅ YES</td></tr>";
        } else {
            echo "<tr><td>Directory Writable</td><td class='error'>❌ NO</td></tr>";
            echo "<tr><td colspan='2' class='info'>Run: chmod 777 " . realpath($upload_dir) . "</td></tr>";
        }
        
        echo "<tr><td>Full Path</td><td>" . realpath($upload_dir) . "</td></tr>";
        echo "</table>";
        ?>

        <h2>2. PHP Upload Settings</h2>
        <?php
        echo "<table>";
        echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? '✅ Enabled' : '❌ Disabled') . "</td></tr>";
        echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
        echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
        echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
        echo "<tr><td>upload_tmp_dir</td><td>" . (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) . "</td></tr>";
        echo "</table>";
        ?>

        <h2>3. Database Connection</h2>
        <?php
        echo "<table>";
        if ($conn->connect_error) {
            echo "<tr><td>Connection</td><td class='error'>❌ FAILED</td></tr>";
        } else {
            echo "<tr><td>Connection</td><td class='success'>✅ Connected</td></tr>";
            
            // Check gallery table
            $check = $conn->query("SHOW TABLES LIKE 'gallery'");
            if ($check && $check->num_rows > 0) {
                echo "<tr><td>Gallery Table</td><td class='success'>✅ EXISTS</td></tr>";
                
                // Check table structure
                $columns = $conn->query("DESCRIBE gallery");
                echo "<tr><td>Table Columns</td><td>";
                while ($col = $columns->fetch_assoc()) {
                    echo $col['Field'] . " (" . $col['Type'] . ")<br>";
                }
                echo "</td></tr>";
            } else {
                echo "<tr><td>Gallery Table</td><td class='error'>❌ NOT FOUND</td></tr>";
            }
        }
        echo "</table>";
        ?>

        <h2>4. Test Upload Form</h2>
        <form method="POST" enctype="multipart/form-data" style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
            <label>Title:</label><br>
            <input type="text" name="title" value="Test Image" required style="width: 100%; padding: 8px; margin: 5px 0;"><br><br>
            
            <label>Description:</label><br>
            <textarea name="description" style="width: 100%; padding: 8px; margin: 5px 0;">Test description</textarea><br><br>
            
            <label>Category:</label><br>
            <select name="category" style="width: 100%; padding: 8px; margin: 5px 0;">
                <option value="Events">Events</option>
                <option value="Activities">Activities</option>
                <option value="Community">Community</option>
                <option value="Other">Other</option>
            </select><br><br>
            
            <label>Select Image:</label><br>
            <input type="file" name="image" accept="image/*" required style="margin: 5px 0;"><br><br>
            
            <button type="submit" name="test_upload" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Test Upload</button>
        </form>

        <?php
        if (isset($_POST['test_upload'])) {
            echo "<h2>5. Upload Test Results</h2>";
            echo "<table>";
            
            echo "<tr><td>POST Data Received</td><td class='success'>✅ YES</td></tr>";
            echo "<tr><td>Title</td><td>" . htmlspecialchars($_POST['title']) . "</td></tr>";
            echo "<tr><td>Description</td><td>" . htmlspecialchars($_POST['description']) . "</td></tr>";
            echo "<tr><td>Category</td><td>" . htmlspecialchars($_POST['category']) . "</td></tr>";
            
            if (isset($_FILES['image'])) {
                echo "<tr><td>File Received</td><td class='success'>✅ YES</td></tr>";
                echo "<tr><td>File Name</td><td>" . $_FILES['image']['name'] . "</td></tr>";
                echo "<tr><td>File Size</td><td>" . round($_FILES['image']['size'] / 1024, 2) . " KB</td></tr>";
                echo "<tr><td>File Type</td><td>" . $_FILES['image']['type'] . "</td></tr>";
                echo "<tr><td>Upload Error Code</td><td>" . $_FILES['image']['error'] . "</td></tr>";
                
                if ($_FILES['image']['error'] == 0) {
                    echo "<tr><td>Upload Status</td><td class='success'>✅ NO ERRORS</td></tr>";
                    
                    // Try to move file
                    $target_dir = "../uploads/";
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $new_filename = 'test_' . time() . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        echo "<tr><td>File Move</td><td class='success'>✅ SUCCESS</td></tr>";
                        echo "<tr><td>Saved As</td><td>" . $new_filename . "</td></tr>";
                        echo "<tr><td>Full Path</td><td>" . realpath($target_file) . "</td></tr>";
                        
                        // Try database insert
                        $title = $conn->real_escape_string($_POST['title']);
                        $desc = $conn->real_escape_string($_POST['description']);
                        $cat = $conn->real_escape_string($_POST['category']);
                        
                        $sql = "INSERT INTO gallery (title, description, category, image, created_at) 
                                VALUES ('$title', '$desc', '$cat', '$new_filename', NOW())";
                        
                        if ($conn->query($sql)) {
                            echo "<tr><td>Database Insert</td><td class='success'>✅ SUCCESS</td></tr>";
                            echo "<tr><td colspan='2' class='success'><strong>✅ EVERYTHING WORKS! You can now use the gallery normally.</strong></td></tr>";
                        } else {
                            echo "<tr><td>Database Insert</td><td class='error'>❌ FAILED</td></tr>";
                            echo "<tr><td>Error</td><td>" . $conn->error . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td>File Move</td><td class='error'>❌ FAILED</td></tr>";
                        echo "<tr><td>Target</td><td>" . $target_file . "</td></tr>";
                    }
                } else {
                    echo "<tr><td>Upload Status</td><td class='error'>❌ ERROR " . $_FILES['image']['error'] . "</td></tr>";
                    $errors = array(
                        1 => 'File exceeds upload_max_filesize',
                        2 => 'File exceeds MAX_FILE_SIZE',
                        3 => 'File was only partially uploaded',
                        4 => 'No file was uploaded',
                        6 => 'Missing temporary folder',
                        7 => 'Failed to write to disk',
                        8 => 'PHP extension stopped upload'
                    );
                    if (isset($errors[$_FILES['image']['error']])) {
                        echo "<tr><td>Error Details</td><td>" . $errors[$_FILES['image']['error']] . "</td></tr>";
                    }
                }
            } else {
                echo "<tr><td>File Received</td><td class='error'>❌ NO</td></tr>";
            }
            
            echo "</table>";
        }
        ?>

        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>⚠️ Note:</strong> Delete this file after testing! It's for diagnostic purposes only.
        </div>
    </div>
</body>
</html>