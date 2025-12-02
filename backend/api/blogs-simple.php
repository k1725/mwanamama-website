<?php
/**
 * Simple Blog API - Bulletproof version
 */

// Prevent any output before JSON
ob_start();

// Suppress errors in JSON output
error_reporting(0);
ini_set('display_errors', 0);

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mwanamama_db";

// Response array
$response = ['success' => false, 'message' => 'Unknown error'];

try {
    // Connect to database
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    $conn->set_charset("utf8mb4");
    
    // Get parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Build query
    if ($id > 0) {
        // Single blog
        $sql = "SELECT * FROM blogs WHERE id = ? AND status = 'published'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
    } else {
        // Multiple blogs
        $sql = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($id > 0) {
        // Single blog response
        if ($result->num_rows > 0) {
            $blog = $result->fetch_assoc();
            
            // Update views
            $update_sql = "UPDATE blogs SET views = views + 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();
            
            $response = [
                'success' => true,
                'data' => [
                    'id' => (int)$blog['id'],
                    'title' => $blog['title'] ?: '',
                    'slug' => $blog['slug'] ?: '',
                    'author' => $blog['author'] ?: 'Admin',
                    'category' => $blog['category'] ?: 'General',
                    'content' => $blog['content'] ?: '',
                    'excerpt' => $blog['excerpt'] ?: substr(strip_tags($blog['content'] ?: ''), 0, 150) . '...',
                    'image' => $blog['image'] ? '/Mwanamama-Website/backend/uploads/' . $blog['image'] : '/Mwanamama-Website/images/default-blog.jpg',
                    'views' => (int)($blog['views'] ?: 0),
                    'created_at' => $blog['created_at'] ?: date('Y-m-d H:i:s'),
                    'formatted_date' => date('F j, Y', strtotime($blog['created_at'] ?: 'now'))
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'Blog not found'];
        }
    } else {
        // Multiple blogs response
        $blogs = [];
        
        while ($row = $result->fetch_assoc()) {
            $content = $row['content'] ?: '';
            $excerpt = $row['excerpt'] ?: '';
            
            if (empty($excerpt)) {
                $excerpt = strip_tags($content);
                $excerpt = substr($excerpt, 0, 150);
                if (strlen($excerpt) >= 150) {
                    $excerpt .= '...';
                }
            }
            
            $blogs[] = [
                'id' => (int)$row['id'],
                'title' => $row['title'] ?: 'Untitled',
                'slug' => $row['slug'] ?: '',
                'author' => $row['author'] ?: 'Admin',
                'category' => $row['category'] ?: 'General',
                'content' => $content,
                'excerpt' => $excerpt,
                'image' => $row['image'] ? '/Mwanamama-Website/backend/uploads/' . $row['image'] : '/Mwanamama-Website/images/default-blog.jpg',
                'views' => (int)($row['views'] ?: 0),
                'created_at' => $row['created_at'] ?: date('Y-m-d H:i:s'),
                'formatted_date' => date('F j, Y', strtotime($row['created_at'] ?: 'now'))
            ];
        }
        
        // Get total count
        $count_sql = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
        $count_result = $conn->query($count_sql);
        $count_row = $count_result->fetch_assoc();
        $total = (int)$count_row['total'];
        
        $response = [
            'success' => true,
            'data' => $blogs,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total
            ]
        ];
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Clear any buffered output
ob_end_clean();

// Output clean JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>