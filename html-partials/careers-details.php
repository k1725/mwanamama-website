<?php
include '../includes/config.php';

// Validate Job ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid Job Request. No Job ID Provided.</div></div>";
    exit;
}

$id = (int)$_GET['id'];

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM careers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();

if (!$job) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Career Not Found or Removed.</div></div>";
    exit;
}

// Update job views
$conn->query("UPDATE careers SET views = views + 1 WHERE id = $id");

// Format deadline
$deadline = $job['deadline'] ? date("F j, Y", strtotime($job['deadline'])) : "Not Specified";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $job['title']; ?> - Job Details</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">

    <a href="careers.php" class="btn btn-secondary mb-3">&larr; Back to Careers</a>

    <h2><?php echo $job['title']; ?></h2>
    <p><strong>Department:</strong> <?php echo $job['department']; ?></p>
    <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
    <p><strong>Type:</strong> <?php echo $job['employment_type']; ?></p>
    <p><strong>Experience Level:</strong> <?php echo $job['experience_level']; ?></p>
    <p><strong>Application Deadline:</strong> <?php echo $deadline; ?></p>
    <p><strong>Views:</strong> <?php echo $job['views']; ?></p>

    <hr>

    <h4>Job Description</h4>
    <p><?php echo nl2br($job['description']); ?></p>

    <h4>Responsibilities</h4>
    <p><?php echo nl2br($job['responsibilities']); ?></p>

    <h4>Requirements</h4>
    <p><?php echo nl2br($job['requirements']); ?></p>

    <?php if (!empty($job['benefits'])): ?>
    <h4>Benefits</h4>
    <p><?php echo nl2br($job['benefits']); ?></p>
    <?php endif; ?>

    <?php if (!empty($job['salary_range'])): ?>
    <h4>Salary Range</h4>
    <p><?php echo $job['salary_range']; ?></p>
    <?php endif; ?>

</div>

</body>
</html>
