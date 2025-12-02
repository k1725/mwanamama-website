<?php
// Ensure this path is correct relative to the file's location
include_once('../includes/config.php'); 

header('Content-Type: application/json');

// The $conn object is now available globally from config.php

// Set up limit and offset for Load More functionality
// Use ternary operator and (int) cast for safe input
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : POSTS_PER_PAGE; 
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($conn->connect_error) {
    // Return an error message if connection fails
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed (via config.php)."]);
    exit();
}

// Query to select only active jobs, ordered by newest first (id DESC)
$sql = "SELECT id, job_title, department, location, employment_type, experience_level, salary_range, description, responsibilities, requirements, benefits, application_deadline, views_count, applications_count FROM careers WHERE status = 'active' ORDER BY created_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$careers = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $careers[] = $row;
    }
}

// Output the results as JSON
echo json_encode($careers);

$stmt->close();
// IMPORTANT: Do NOT close $conn here, as other parts of the site might need it later.
// $conn->close(); // Removed
?>