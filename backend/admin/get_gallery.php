<?php
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM gallery WHERE id = $id");
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Image not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>