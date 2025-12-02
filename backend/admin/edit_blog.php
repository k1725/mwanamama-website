<?php
include('../includes/config.php');

if (!isset($_GET['id'])) {
    die("Blog ID missing!");
}
$id = $_GET['id'];
$blog = $conn->query("SELECT * FROM blogs WHERE id='$id'")->fetch_assoc();

if (isset($_POST['update_blog'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $content = $_POST['content'];

    // If new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = "../uploads/blog_images/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        // Delete old image
        if (file_exists("../uploads/blog_images/" . $blog['image'])) {
            unlink("../uploads/blog_images/" . $blog['image']);
        }

        $sql = "UPDATE blogs SET title='$title', category='$category', author='$author', content='$content', image='$image' WHERE id='$id'";
    } else {
        $sql = "UPDATE blogs SET title='$title', category='$category', author='$author', content='$content' WHERE id='$id'";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Blog updated successfully!'); window.location='manage_blogs.php';</script>";
    } else {
        echo "<script>alert('Failed to update blog!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Blog</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body class="p-4">
<h2 class="mb-4">Edit Blog Post</h2>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" value="<?= $blog['title']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Category</label>
        <input type="text" name="category" value="<?= $blog['category']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Author</label>
        <input type="text" name="author" value="<?= $blog['author']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Content</label>
        <textarea name="content" class="form-control" rows="6" required><?= $blog['content']; ?></textarea>
    </div>
    <div class="mb-3">
        <label>Current Image</label><br>
        <img src="../uploads/blog_images/<?= $blog['image']; ?>" alt="" width="150" class="mb-2 rounded"><br>
        <input type="file" name="image" class="form-control">
    </div>
    <button type="submit" name="update_blog" class="btn btn-primary">Update Blog</button>
    <a href="manage_blogs.php" class="btn btn-secondary">Back</a>
</form>
</body>
</html>
