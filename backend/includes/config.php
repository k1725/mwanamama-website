<?php
/**
 * Database Configuration File
 * Mwanamama Admin Dashboard
 */

// Configure session settings BEFORE starting session
if (session_status() === PHP_SESSION_NONE) {
    // Fix session save path if needed
    $session_path = ini_get('session.save_path');
    if (empty($session_path) || !is_writable($session_path)) {
        // Use system temp directory as fallback
        ini_set('session.save_path', sys_get_temp_dir());
    }
    
    // Session Configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    
    // Start session
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');           // Database host (usually localhost)
define('DB_USER', 'root');                // Database username (change this)
define('DB_PASS', '');                    // Database password (change this)
define('DB_NAME', 'mwanamama_db');        // Database name

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Site Configuration
define('SITE_NAME', 'Mwanamama');
define('SITE_URL', 'http://localhost/mwanamama');  // Change this to your domain
define('ADMIN_EMAIL', 'admin@mwanamama.com');

// Upload Configuration
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);

// Pagination
define('POSTS_PER_PAGE', 10);

// Session Configuration
// ini_set('session.cookie_httponly', 1);
// ini_set('session.use_only_cookies', 1);
// ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Helper Functions
 */

// Sanitize input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Generate slug from title
function generate_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Format date
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Get time ago
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
    $lengths = array(60, 60, 24, 7, 4.35, 12);
    
    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
        $difference /= $lengths[$j];
    }
    
    $difference = round($difference);
    
    if ($difference != 1) {
        $periods[$j] .= 's';
    }
    
    return "$difference $periods[$j] ago";
}

// Upload image
function upload_image($file, $prefix = 'img') {
    $target_dir = UPLOAD_DIR;
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large. Max 5MB'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    $target_file = $target_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

// Delete image
function delete_image($filename) {
    $file_path = UPLOAD_DIR . $filename;
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

// Get admin info
function get_admin_info() {
    if (is_logged_in()) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? 'Admin',
            'email' => $_SESSION['admin_email'] ?? ''
        ];
    }
    return null;
}

// Success message
function set_success_message($message) {
    $_SESSION['success_message'] = $message;
}

// Error message
function set_error_message($message) {
    $_SESSION['error_message'] = $message;
}

// Display success message
function display_success_message() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> ' . $_SESSION['success_message'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['success_message']);
    }
}

// Display error message
function display_error_message() {
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> ' . $_SESSION['error_message'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['error_message']);
    }
}

/**
 * Database Helper Functions
 */

// Get total count
function get_total_count($table) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Get recent items
function get_recent_items($table, $limit = 5) {
    global $conn;
    $result = $conn->query("SELECT * FROM $table ORDER BY created_at DESC LIMIT $limit");
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>