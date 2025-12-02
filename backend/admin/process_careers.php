<?php
// Ensure this path is correct relative to the file's location
include_once('../includes/config.php'); 
require_login(); // Security check using the function from config.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    // Use clean_input for user-entered strings
    $job_title = clean_input($_POST['job_title'] ?? '');
    $department = clean_input($_POST['department'] ?? '');
    
    // Enum/Date fields require less cleaning but still good practice to validate
    $employment_type = $_POST['employment_type'] ?? ''; 
    $description = clean_input($_POST['description'] ?? '');
    $deadline = $_POST['application_deadline'] ?? ''; // Date format validation should be added
    $status = $_POST['status'] ?? 'draft';

    if ($action === 'add') {
        // Sample fields added to match the database structure, set to placeholders
        $sql = "INSERT INTO careers (job_title, department, employment_type, description, application_deadline, status, location, experience_level, responsibilities, requirements) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Example placeholders for required but currently missing fields in the form
        $location = 'TBD';
        $experience_level = 'Entry';
        $responsibilities = 'To be detailed.';
        $requirements = 'To be detailed.';
        
        $stmt = $conn->prepare($sql);
        // Bind all parameters as strings (s)
        $stmt->bind_param("ssssssssss", 
            $job_title, $department, $employment_type, $description, $deadline, $status, 
            $location, $experience_level, $responsibilities, $requirements);

        if ($stmt->execute()) {
            set_success_message("New job posting for '{$job_title}' added successfully.");
        } else {
            set_error_message("Error adding job posting: " . $stmt->error);
        }
        $stmt->close();
    } 
    // Add 'edit' and 'delete' logic here...

    // Redirect back to the management page after processing
    header("Location: manage_careers.php");
    exit();
}
// IMPORTANT: Do NOT close $conn here, let the script end naturally
?>