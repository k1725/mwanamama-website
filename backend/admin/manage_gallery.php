<?php 
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM gallery WHERE id = $id");
    header('Location: manage_gallery.php?success=deleted');
    exit();
}

// Fetch all gallery images
$result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
$images = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="main-content">
    <div class="dashboard-header">
        <div>
            <h1 class="page-title">Gallery Management</h1>
            <p class="page-subtitle">Upload and manage your gallery images</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                <i class="bi bi-cloud-upload"></i> Upload Image
            </button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i>
            <?php 
                if ($_GET['success'] == 'added') echo 'Image uploaded successfully!';
                if ($_GET['success'] == 'updated') echo 'Image updated successfully!';
                if ($_GET['success'] == 'deleted') echo 'Image deleted successfully!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Gallery Grid -->
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title"><i class="bi bi-images"></i> All Images (<?php echo count($images); ?>)</h2>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchGallery" placeholder="Search images...">
            </div>
        </div>
        <div class="card-body">
            <?php if (count($images) > 0): ?>
                <div class="gallery-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="gallery-item" data-title="<?php echo htmlspecialchars($image['title']); ?>">
                            <div class="gallery-image">
                                <img src="../uploads/<?php echo $image['image']; ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                                <div class="gallery-overlay">
                                    <button class="btn-gallery-action" onclick="viewImage('<?php echo $image['image']; ?>')" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-gallery-action" onclick="editGallery(<?php echo $image['id']; ?>)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-gallery-action" onclick="deleteGallery(<?php echo $image['id']; ?>)" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="gallery-info">
                                <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('M d, Y', strtotime($image['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-image"></i>
                    <p>No images yet. Upload your first image!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Gallery Modal -->
<div class="modal fade" id="addGalleryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Upload Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_gallery.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Image Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control">
                            <option value="Events">Events</option>
                            <option value="Activities">Activities</option>
                            <option value="Community">Community</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Select Image *</label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="galleryImage" required>
                        <div class="image-preview" id="galleryImagePreview"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_gallery" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Gallery Modal -->
<div class="modal fade" id="editGalleryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_gallery.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="gallery_id" id="edit_gallery_id">
                <div class="modal-body" id="editGalleryContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_gallery" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Image Modal -->
<div class="modal fade" id="viewImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImageSrc" src="" alt="Image preview" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Search functionality
document.getElementById('searchGallery').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.gallery-item');
    
    items.forEach(item => {
        const title = item.dataset.title.toLowerCase();
        item.style.display = title.includes(searchTerm) ? '' : 'none';
    });
});

// Image preview for add modal
document.getElementById('galleryImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('galleryImagePreview').innerHTML = 
                `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }
});

// View image function
function viewImage(imagePath) {
    document.getElementById('viewImageSrc').src = `../uploads/${imagePath}`;
    const viewModal = new bootstrap.Modal(document.getElementById('viewImageModal'));
    viewModal.show();
}

// Edit gallery function
function editGallery(id) {
    fetch(`get_gallery.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_gallery_id').value = data.id;
            document.getElementById('editGalleryContent').innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Image Title *</label>
                    <input type="text" name="title" class="form-control" value="${data.title}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">${data.description || ''}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="Events" ${data.category == 'Events' ? 'selected' : ''}>Events</option>
                        <option value="Activities" ${data.category == 'Activities' ? 'selected' : ''}>Activities</option>
                        <option value="Community" ${data.category == 'Community' ? 'selected' : ''}>Community</option>
                        <option value="Other" ${data.category == 'Other' ? 'selected' : ''}>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Change Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="current-image mt-2">
                        <img src="../uploads/${data.image}" alt="Current" style="max-width: 200px;">
                    </div>
                </div>
            `;
            
            const editModal = new bootstrap.Modal(document.getElementById('editGalleryModal'));
            editModal.show();
        });
}

// Delete gallery function
function deleteGallery(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        window.location.href = `manage_gallery.php?delete=${id}`;
    }
}
</script>