<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mwanamama Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<header class="admin-header">
    <div class="header-left">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="logo">
            <span class="logo-icon">🌸</span>
            <span class="logo-text">Mwanamama</span>
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search...">
        </div>
        
        <div class="header-notifications">
            <button class="notification-btn">
                <i class="bi bi-bell"></i>
                <span class="notification-badge">3</span>
            </button>
        </div>
        
        <div class="header-profile">
            <div class="profile-dropdown">
                <button class="profile-btn">
                    <div class="profile-avatar">A</div>
                    <span class="profile-name">Admin</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="profile-menu">
                    <a href="profile.php"><i class="bi bi-person"></i> Profile</a>
                    <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                    <hr>
                    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>