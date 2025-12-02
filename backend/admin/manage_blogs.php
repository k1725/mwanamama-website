<?php 
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM blogs WHERE id = $id");
    header('Location: manage_blogs.php?success=deleted');
    exit();
}

// Fetch all blogs
$result = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="main-content">
    <div class="dashboard-header">
        <div>
            <h1 class="page-title">Blog Management</h1>
            <p class="page-subtitle">Create and manage your blog posts</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
                <i class="bi bi-plus-circle"></i> Add New Blog
            </button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i>
            <?php 
                if ($_GET['success'] == 'added') echo 'Blog post added successfully!';
                if ($_GET['success'] == 'updated') echo 'Blog post updated successfully!';
                if ($_GET['success'] == 'deleted') echo 'Blog post deleted successfully!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Blogs Table -->
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title"><i class="bi bi-journal-text"></i> All Blog Posts</h2>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchBlog" placeholder="Search blogs...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($blogs) > 0): ?>
                            <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td><?php echo $blog['id']; ?></td>
                                    <td>
                                        <div class="blog-title-cell">
                                            <?php if (!empty($blog['image'])): ?>
                                                <img src="../uploads/<?php echo $blog['image']; ?>" alt="Blog thumbnail" class="table-thumbnail">
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($blog['title']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($blog['author'] ?? 'Admin'); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($blog['category'] ?? 'General'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo ($blog['status'] ?? 'published') == 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($blog['status'] ?? 'published'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-edit" onclick="editBlog(<?php echo $blog['id']; ?>)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteBlog(<?php echo $blog['id']; ?>)" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>No blog posts yet. Create your first post!</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add New Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_blog.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" value="Admin">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control">
                                <option value="General">General</option>
                                <option value="Health">Health</option>
                                <option value="Lifestyle">Lifestyle</option>
                                <option value="Parenting">Parenting</option>
                                <option value="Wellness">Wellness</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Content *</label>
                            <textarea name="content" class="form-control" rows="6" required></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" id="blogImage">
                            <div class="image-preview" id="blogImagePreview"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_blog" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Blog Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Blog Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_blog.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="blog_id" id="edit_blog_id">
                <div class="modal-body" id="editBlogContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_blog" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Blog Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Search functionality
document.getElementById('searchBlog').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Image preview for add modal
document.getElementById('blogImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('blogImagePreview').innerHTML = 
                `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }
});

// Edit blog function
function editBlog(id) {
    // Load blog data via AJAX
    fetch(`get_blog.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_blog_id').value = data.id;
            document.getElementById('editBlogContent').innerHTML = `
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="${data.title}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" name="author" class="form-control" value="${data.author || 'Admin'}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control">
                            <option value="General" ${data.category == 'General' ? 'selected' : ''}>General</option>
                            <option value="Health" ${data.category == 'Health' ? 'selected' : ''}>Health</option>
                            <option value="Lifestyle" ${data.category == 'Lifestyle' ? 'selected' : ''}>Lifestyle</option>
                            <option value="Parenting" ${data.category == 'Parenting' ? 'selected' : ''}>Parenting</option>
                            <option value="Wellness" ${data.category == 'Wellness' ? 'selected' : ''}>Wellness</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Content *</label>
                        <textarea name="content" class="form-control" rows="6" required>${data.content}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        ${data.image ? `<div class="current-image"><img src="../uploads/${data.image}" alt="Current"></div>` : ''}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="published" ${data.status == 'published' ? 'selected' : ''}>Published</option>
                            <option value="draft" ${data.status == 'draft' ? 'selected' : ''}>Draft</option>
                        </select>
                    </div>
                </div>
            `;
            
            const editModal = new bootstrap.Modal(document.getElementById('editBlogModal'));
            editModal.show();
        });
}

// Delete blog function
function deleteBlog(id) {
    if (confirm('Are you sure you want to delete this blog post?')) {
        window.location.href = `manage_blogs.php?delete=${id}`;
    }
}
</script>