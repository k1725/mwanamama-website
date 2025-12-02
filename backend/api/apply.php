<?php
include '../includes/config.php';

$career_id = $_POST['career_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$target_dir = "../uploads/cv/";
if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

$cv_file = $target_dir . time() . "_" . basename($_FILES["cv_file"]["name"]);
move_uploaded_file($_FILES["cv_file"]["tmp_name"], $cv_file);

$stmt = $conn->prepare("INSERT INTO career_applications (career_id, full_name, email, phone, cv_file) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $career_id, $full_name, $email, $phone, $cv_file);
$stmt->execute();

header("Location: /Mwanamama-Website/html-partials/careers-details.php?id=$career_id&success=1");
exit;
