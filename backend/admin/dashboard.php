<?php 
session_start();
include '../includes/config.php';

// Check if user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit();
// }

// Fetch statistics
$stats = [];

// Total Blogs
$result = $conn->query("SELECT COUNT(*) AS total FROM blogs");
$stats['blogs'] = $result->fetch_assoc()['total'];

// Total Gallery Images
$result = $conn->query("SELECT COUNT(*) AS total FROM gallery");
$stats['gallery'] = $result->fetch_assoc()['total'];

// Recent Blogs (last 5)
$result = $conn->query("SELECT id, title, created_at FROM blogs ORDER BY created_at DESC LIMIT 5");
$recent_blogs = $result->fetch_all(MYSQLI_ASSOC);

// Recent Gallery Images (last 5)
$result = $conn->query("SELECT id, title, created_at FROM gallery ORDER BY created_at DESC LIMIT 5");
// $recent_gallery = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="main-content">
    <div class="dashboard-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, Admin! Here's what's happening today.</p>
        </div>
        <div class="header-actions">
            <span class="date-time" id="currentDateTime"></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="bi bi-journal-text"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-number"><?php echo $stats['blogs']; ?></h3>
                <p class="stat-label">Total Blogs</p>
            </div>
            <a href="manage_blogs.php" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="bi bi-images"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-number"><?php echo $stats['gallery']; ?></h3>
                <p class="stat-label">Gallery Images</p>
            </div>
            <a href="manage_gallery.php" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="bi bi-eye"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-number">0</h3>
                <p class="stat-label">Total Views</p>
            </div>
            <a href="#" class="stat-link">Analytics <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-number">0</h3>
                <p class="stat-label">Visitors Today</p>
            </div>
            <a href="#" class="stat-link">Details <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="content-grid">
        <!-- Recent Blogs -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title"><i class="bi bi-journal-text"></i> Recent Blogs</h2>
                <a href="manage_blogs.php" class="btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($recent_blogs) > 0): ?>
                    <div class="list-group">
                        <?php foreach ($recent_blogs as $blog): ?>
                            <div class="list-item">
                                <div class="list-item-content">
                                    <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> 
                                        <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                                    </small>
                                </div>
                                <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn-icon">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No blogs yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Gallery -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title"><i class="bi bi-images"></i> Recent Gallery</h2>
                <a href="manage_gallery.php" class="btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($recent_gallery) > 0): ?>
                    <div class="list-group">
                        <?php foreach ($recent_gallery as $image): ?>
                            <div class="list-item">
                                <div class="list-item-content">
                                    <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> 
                                        <?php echo date('M d, Y', strtotime($image['created_at'])); ?>
                                    </small>
                                </div>
                                <a href="edit_gallery.php?id=<?php echo $image['id']; ?>" class="btn-icon">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No gallery images yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="section-title">Quick Actions</h2>
        <div class="action-buttons">
            <a href="manage_blogs.php?action=new" class="action-btn blue">
                <i class="bi bi-plus-circle"></i>
                <span>New Blog Post</span>
            </a>
            <a href="manage_gallery.php?action=new" class="action-btn purple">
                <i class="bi bi-cloud-upload"></i>
                <span>Upload Image</span>
            </a>
            <a href="settings.php" class="action-btn green">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Update date and time
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
}

updateDateTime();
setInterval(updateDateTime, 60000);
</script>