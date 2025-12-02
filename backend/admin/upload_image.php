<?php include('../includes/config.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Image to Gallery</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body class="p-5">

<h2>Upload to Gallery</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Caption</label>
        <input type="text" name="caption" class="form-control">
    </div>

    <div class="mb-3">
        <label>Choose Image</label>
        <input type="file" name="image" class="form-control" required>
    </div>

    <button type="submit" name="upload" class="btn btn-success">Upload</button>
</form>

<?php
if (isset($_POST['upload'])) {
    $caption = $_POST['caption'];
    $target_dir = "../uploads/gallery/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO gallery (image_path, caption) VALUES ('$image_name', '$caption')";
        if ($conn->query($sql)) {
            echo "<script>alert('Image uploaded successfully!');</script>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Failed to upload image.</p>";
    }
}
?>

</body>
</html>
