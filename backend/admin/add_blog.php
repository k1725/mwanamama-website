<?php include('../includes/config.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Blog</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body class="p-5">

<h2>Add New Blog Post</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Category</label>
        <input type="text" name="category" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Author</label>
        <input type="text" name="author" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Short Description</label>
        <textarea name="short_description" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
        <label>Full Content</label>
        <textarea name="full_content" class="form-control" rows="6" required></textarea>
    </div>

    <div class="mb-3">
        <label>Image</label>
        <input type="file" name="image" class="form-control" required>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Publish Blog</button>
</form>

<?php
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $short_description = $_POST['short_description'];
    $full_content = $_POST['full_content'];

    // handle image
    $target_dir = "../uploads/blog_images/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO blogs (title, category, author, image, short_description, full_content)
                VALUES ('$title', '$category', '$author', '$image_name', '$short_description', '$full_content')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Blog added successfully!');</script>";
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
