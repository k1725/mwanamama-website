<?php
/**
 * Blogs API Endpoint
 * Returns blog posts in JSON format
 */

// Disable error output to prevent breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Start output buffering to catch any accidental output
ob_start();

include '../includes/config.php';

// Helper functions
function create_excerpt($content, $length = 150) {
    if (empty($content)) return '';
    $content = strip_tags($content);
    if (strlen($content) <= $length) {
        return $content;
    }
    return substr($content, 0, $length) . '...';
}

function format_date($date) {
    if (empty($date)) return date('F j, Y');
    return date('F j, Y', strtotime($date));
}

// Path configuration
$base_path = '/Mwanamama-Website/';
$upload_path = $base_path . 'backend/uploads/';
$default_image = $base_path . 'images/default-blog.jpg';

// Get parameters
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Build query
    if ($id > 0) {
        // Fetch single blog
        $sql = "SELECT * FROM blogs WHERE id = $id AND status = 'published'";
    } else {
        // Fetch multiple blogs
        $sql = "SELECT * FROM blogs WHERE status = 'published'";
        
        if ($category) {
            $sql .= " AND category = '$category'";
        }
        
        if ($search) {
            $sql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    }

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception($conn->error);
    }

    if ($id > 0 && $result->num_rows > 0) {
        // Single blog
        $blog = $result->fetch_assoc();
        
        // Update view count
        $conn->query("UPDATE blogs SET views = views + 1 WHERE id = $id");
        
        // Format response
        $response = [
            'success' => true,
            'data' => [
                'id' => $blog['id'],
                'title' => $blog['title'] ?? '',
                'slug' => $blog['slug'] ?? '',
                'author' => $blog['author'] ?? 'Admin',
                'category' => $blog['category'] ?? 'General',
                'content' => $blog['content'] ?? '',
                'excerpt' => !empty($blog['excerpt']) ? $blog['excerpt'] : create_excerpt($blog['content'] ?? ''),
                'image' => !empty($blog['image']) ? $upload_path . $blog['image'] : $default_image,
                'views' => $blog['views'] ?? 0,
                'created_at' => $blog['created_at'] ?? date('Y-m-d H:i:s'),
                'formatted_date' => format_date($blog['created_at'] ?? date('Y-m-d H:i:s'))
            ]
        ];
    } else {
        // Multiple blogs
        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogs[] = [
                'id' => $row['id'],
                'title' => $row['title'] ?? '',
                'slug' => $row['slug'] ?? '',
                'author' => $row['author'] ?? 'Admin',
                'category' => $row['category'] ?? 'General',
                'content' => $row['content'] ?? '',
                'excerpt' => !empty($row['excerpt']) ? $row['excerpt'] : create_excerpt($row['content'] ?? ''),
                'image' => !empty($row['image']) ? $upload_path . $row['image'] : $default_image,
                'views' => $row['views'] ?? 0,
                'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
                'formatted_date' => format_date($row['created_at'] ?? date('Y-m-d H:i:s'))
            ];
        }
        
        // Get total count
        $count_sql = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
        if ($category) {
            $count_sql .= " AND category = '$category'";
        }
        if ($search) {
            $count_sql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
        }
        $count_result = $conn->query($count_sql);
        $total = 0;
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $total = $count_row['total'];
        }
        
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
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Failed to fetch blogs',
        'error' => $e->getMessage()
    ];
}

// Clear any buffered output
ob_end_clean();

// Output JSON
echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
<?php
// Build query
if ($id > 0) {
    // Fetch single blog
    $sql = "SELECT * FROM blogs WHERE id = $id AND status = 'published'";
} else {
    // Fetch multiple blogs
    $sql = "SELECT * FROM blogs WHERE status = 'published'";
    
    if ($category) {
        $sql .= " AND category = '$category'";
    }
    
    if ($search) {
        $sql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
}

$result = $conn->query($sql);

if ($result) {
    if ($id > 0 && $result->num_rows > 0) {
        // Single blog
        $blog = $result->fetch_assoc();
        
        // Update view count
        $conn->query("UPDATE blogs SET views = views + 1 WHERE id = $id");
        
        // Format response
        $response = [
            'success' => true,
            'data' => [
                'id' => $blog['id'],
                'title' => $blog['title'],
                'slug' => $blog['slug'] ?? '',
                'author' => $blog['author'] ?? 'Admin',
                'category' => $blog['category'] ?? 'General',
                'content' => $blog['content'],
                'excerpt' => $blog['excerpt'] ?? substr(strip_tags($blog['content']), 0, 150) . '...',
                'image' => $blog['image'] ? '../backend/uploads/' . $blog['image'] : 'images/default-blog.jpg',
                'views' => $blog['views'] ?? 0,
                'created_at' => $blog['created_at'],
                'formatted_date' => date('F j, Y', strtotime($blog['created_at']))
            ]
        ];
    } else {
        // Multiple blogs
        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogs[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'] ?? '',
                'author' => $row['author'] ?? 'Admin',
                'category' => $row['category'] ?? 'General',
                'content' => $row['content'],
                'excerpt' => $row['excerpt'] ?? substr(strip_tags($row['content']), 0, 150) . '...',
                'image' => $row['image'] ? '../backend/uploads/' . $row['image'] : 'images/default-blog.jpg',
                'views' => $row['views'] ?? 0,
                'created_at' => $row['created_at'],
                'formatted_date' => date('F j, Y', strtotime($row['created_at']))
            ];
        }
        
        // Get total count
        $count_sql = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
        if ($category) {
            $count_sql .= " AND category = '$category'";
        }
        if ($search) {
            $count_sql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
        }
        $count_result = $conn->query($count_sql);
        $total = $count_result->fetch_assoc()['total'];
        
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
} else {
    $response = [
        'success' => false,
        'message' => 'Failed to fetch blogs',
        'error' => $conn->error
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>