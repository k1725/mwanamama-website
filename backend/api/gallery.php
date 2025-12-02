<?php
/**
 * Gallery API Endpoint
 * Returns gallery images in JSON format
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include '../includes/config.php';

// Define the absolute project path for images
$project_base_path = '/Mwanamama-Website/';
$upload_path = $project_base_path . 'backend/uploads/';
$default_image = $project_base_path . 'images/default-gallery.jpg';


// Get parameters
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Build query
if ($id > 0) {
    // Fetch single image
    $sql = "SELECT * FROM gallery WHERE id = $id AND status = 'active'";
} else {
    // Fetch multiple images
    $sql = "SELECT * FROM gallery WHERE status = 'active'";
    
    if ($category) {
        $sql .= " AND category = '$category'";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
}

$result = $conn->query($sql);

if ($result) {
    if ($id > 0 && $result->num_rows > 0) {
        // Single image
        $image = $result->fetch_assoc();
        
        // Update view count
        $conn->query("UPDATE gallery SET views = views + 1 WHERE id = $id");
        
        // Format response
        $response = [
            'success' => true,
            'data' => [
                'id' => $image['id'],
                'title' => $image['title'],
                'description' => $image['description'] ?? '',
                'category' => $image['category'] ?? 'General',
                // **FIXED PATH HERE**
                'image' => $upload_path . $image['image'],
                'views' => $image['views'] ?? 0,
                'created_at' => $image['created_at'],
                'formatted_date' => date('F j, Y', strtotime($image['created_at']))
            ]
        ];
    } else {
        // Multiple images
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'] ?? '',
                'category' => $row['category'] ?? 'General',
                // **FIXED PATH HERE**
                'image' => $upload_path . $row['image'],
                'views' => $row['views'] ?? 0,
                'created_at' => $row['created_at'],
                'formatted_date' => date('F j, Y', strtotime($row['created_at']))
            ];
        }
        
        // Get total count
        $count_sql = "SELECT COUNT(*) as total FROM gallery WHERE status = 'active'";
        if ($category) {
            $count_sql .= " AND category = '$category'";
        }
        $count_result = $conn->query($count_sql);
        $total = $count_result->fetch_assoc()['total'];
        
        $response = [
            'success' => true,
            'data' => $images,
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
        'message' => 'Failed to fetch gallery images',
        'error' => $conn->error
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>