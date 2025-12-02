<?php
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Add Gallery Image
if (isset($_POST['add_gallery'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validate image
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $new_filename;
            }
        }
    }
    
    if ($image) {
        $sql = "INSERT INTO gallery (title, description, category, image, created_at) 
                VALUES ('$title', '$description', '$category', '$image', NOW())";
        
        if ($conn->query($sql)) {
            header('Location: manage_gallery.php?success=added');
        } else {
            header('Location: manage_gallery.php?error=failed');
        }
    } else {
        header('Location: manage_gallery.php?error=upload_failed');
    }
    exit();
}

// Update Gallery Image
if (isset($_POST['update_gallery'])) {
    $id = intval($_POST['gallery_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    
    // Handle image upload
    $image_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Delete old image
                $result = $conn->query("SELECT image FROM gallery WHERE id = $id");
                if ($row = $result->fetch_assoc()) {
                    if ($row['image'] && file_exists("../uploads/" . $row['image'])) {
                        unlink("../uploads/" . $row['image']);
                    }
                }
                $image_sql = ", image = '$new_filename'";
            }
        }
    }
    
    $sql = "UPDATE gallery SET 
            title = '$title', 
            description = '$description', 
            category = '$category'
            $image_sql
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        header('Location: manage_gallery.php?success=updated');
    } else {
        header('Location: manage_gallery.php?error=failed');
    }
    exit();
}
?>