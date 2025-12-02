<?php
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Add Blog
if (isset($_POST['add_blog'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $category = $conn->real_escape_string($_POST['category']);
    $content = $conn->real_escape_string($_POST['content']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'blog_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validate image
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $new_filename;
            }
        }
    }
    
    $sql = "INSERT INTO blogs (title, author, category, content, image, status, created_at) 
            VALUES ('$title', '$author', '$category', '$content', '$image', '$status', NOW())";
    
    if ($conn->query($sql)) {
        header('Location: manage_blogs.php?success=added');
    } else {
        header('Location: manage_blogs.php?error=failed');
    }
    exit();
}

// Update Blog
if (isset($_POST['update_blog'])) {
    $id = intval($_POST['blog_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $category = $conn->real_escape_string($_POST['category']);
    $content = $conn->real_escape_string($_POST['content']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Handle image upload
    $image_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'blog_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Delete old image
                $result = $conn->query("SELECT image FROM blogs WHERE id = $id");
                if ($row = $result->fetch_assoc()) {
                    if ($row['image'] && file_exists("../uploads/" . $row['image'])) {
                        unlink("../uploads/" . $row['image']);
                    }
                }
                $image_sql = ", image = '$new_filename'";
            }
        }
    }
    
    $sql = "UPDATE blogs SET 
            title = '$title', 
            author = '$author', 
            category = '$category', 
            content = '$content', 
            status = '$status'
            $image_sql
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        header('Location: manage_blogs.php?success=updated');
    } else {
        header('Location: manage_blogs.php?error=failed');
    }
    exit();
}
?>