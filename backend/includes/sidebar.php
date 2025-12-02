<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="manage_blog.php" class="nav-link <?php echo ($current_page == 'manage_blog.php') ? 'active' : ''; ?>">
                    <i class="bi bi-journal-text"></i>
                    <span class="nav-text">Blog Posts</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="manage_gallery.php" class="nav-link <?php echo ($current_page == 'manage_gallery.php') ? 'active' : ''; ?>">
                    <i class="bi bi-images"></i>
                    <span class="nav-text">Gallery</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="manage_careers.php" class="nav-link <?php echo ($current_page == 'manage_careers.php') ? 'active' : ''; ?>">
                    <i class="bi bi-briefcase"></i>
                    <span class="nav-text">Careers</span>
                </a>
            </li>
            
            <li class="nav-divider"></li>
            
            <li class="nav-item">
                <a href="comments.php" class="nav-link <?php echo ($current_page == 'comments.php') ? 'active' : ''; ?>">
                    <i class="bi bi-chat-dots"></i>
                    <span class="nav-text">Comments</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="users.php" class="nav-link <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Users</span>
                </a>
            </li>
            
            <li class="nav-divider"></li>
            
            <li class="nav-item">
                <a href="analytics.php" class="nav-link <?php echo ($current_page == 'analytics.php') ? 'active' : ''; ?>">
                    <i class="bi bi-graph-up"></i>
                    <span class="nav-text">Analytics</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="settings.php" class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="storage-info">
            <div class="storage-header">
                <i class="bi bi-hdd"></i>
                <span>Storage</span>
            </div>
            <div class="storage-bar">
                <div class="storage-progress" style="width: 65%"></div>
            </div>
            <small>6.5 GB of 10 GB used</small>
        </div>
    </div>
</aside>