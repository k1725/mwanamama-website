<?php
/**
 * Debug Blog API - Shows what's wrong
 */

// Show ALL errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Blog API Debug</h1>";

// Test 1: Database connection
echo "<h2>Test 1: Database Connection</h2>";
include '../includes/config.php';

if ($conn->connect_error) {
    echo "❌ <span style='color: red;'>Connection failed: " . $conn->connect_error . "</span><br>";
    die();
} else {
    echo "✅ <span style='color: green;'>Database connected successfully</span><br>";
    echo "Database: " . DB_NAME . "<br><br>";
}

// Test 2: Check blogs table
echo "<h2>Test 2: Check Blogs Table</h2>";
$check_table = $conn->query("SHOW TABLES LIKE 'blogs'");
if ($check_table && $check_table->num_rows > 0) {
    echo "✅ <span style='color: green;'>Blogs table exists</span><br>";
    
    // Check structure
    $structure = $conn->query("DESCRIBE blogs");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th></tr>";
    while ($col = $structure->fetch_assoc()) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ <span style='color: red;'>Blogs table does NOT exist</span><br>";
    die();
}

// Test 3: Check blog count
echo "<h2>Test 3: Check Blog Data</h2>";
$count = $conn->query("SELECT COUNT(*) as total FROM blogs");
$total = $count->fetch_assoc()['total'];
echo "Total blogs in database: <strong>$total</strong><br>";

$published = $conn->query("SELECT COUNT(*) as total FROM blogs WHERE status = 'published'");
$pub_total = $published->fetch_assoc()['total'];
echo "Published blogs: <strong>$pub_total</strong><br><br>";

if ($pub_total == 0) {
    echo "⚠️ <span style='color: orange;'>WARNING: No published blogs found!</span><br>";
    echo "You need to add blogs with status = 'published'<br><br>";
}

// Test 4: Fetch blogs
echo "<h2>Test 4: Fetch Published Blogs</h2>";
$sql = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT 3";
echo "Query: <code>$sql</code><br><br>";

$result = $conn->query($sql);

if (!$result) {
    echo "❌ <span style='color: red;'>Query failed: " . $conn->error . "</span><br>";
    die();
}

echo "✅ Query successful. Found <strong>" . $result->num_rows . "</strong> blogs<br><br>";

if ($result->num_rows > 0) {
    echo "<h3>Blog Data:</h3>";
    echo "<table border='1' cellpadding='5' style='width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Image</th><th>Status</th><th>Created</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['author'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['category'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['image'] ?? 'none') . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ <span style='color: red;'>No blogs found with status='published'</span><br>";
}

// Test 5: Try to generate JSON
echo "<h2>Test 5: Generate JSON Response</h2>";

$result = $conn->query("SELECT * FROM blogs WHERE status = 'published' LIMIT 3");
$blogs = [];

while ($row = $result->fetch_assoc()) {
    $blogs[] = [
        'id' => $row['id'],
        'title' => $row['title'] ?? '',
        'author' => $row['author'] ?? 'Admin',
        'category' => $row['category'] ?? 'General',
        'content' => $row['content'] ?? '',
        'excerpt' => isset($row['excerpt']) ? $row['excerpt'] : substr(strip_tags($row['content'] ?? ''), 0, 150),
        'image' => !empty($row['image']) ? '/Mwanamama-Website/backend/uploads/' . $row['image'] : '/Mwanamama-Website/images/default-blog.jpg',
        'views' => $row['views'] ?? 0,
        'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
        'formatted_date' => date('F j, Y', strtotime($row['created_at'] ?? 'now'))
    ];
}

$response = [
    'success' => true,
    'data' => $blogs,
    'pagination' => [
        'total' => $pub_total,
        'limit' => 3,
        'offset' => 0,
        'has_more' => $pub_total > 3
    ]
];

echo "<pre>";
echo json_encode($response, JSON_PRETTY_PRINT);
echo "</pre>";

// Test 6: Check for JSON errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ <span style='color: red;'>JSON Error: " . json_last_error_msg() . "</span><br>";
} else {
    echo "✅ <span style='color: green;'>JSON generated successfully</span><br>";
}

// Test 7: Quick fix SQL
echo "<h2>Quick Fix SQL</h2>";
echo "<p>If you have no published blogs, run this SQL in phpMyAdmin:</p>";
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'>";
echo "-- Check current blogs
SELECT id, title, status FROM blogs;

-- Set all blogs to published
UPDATE blogs SET status = 'published';

-- Or insert a test blog
INSERT INTO blogs (title, author, category, content, status, created_at) 
VALUES ('Test Blog Post', 'Admin', 'General', 'This is a test blog post content.', 'published', NOW());
";
echo "</textarea>";

$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    h1 { color: #333; border-bottom: 3px solid #4F46E5; padding-bottom: 10px; }
    h2 { color: #666; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 30px; }
    table { border-collapse: collapse; margin: 10px 0; }
    table td, table th { border: 1px solid #ddd; padding: 8px; text-align: left; }
    table th { background: #f5f5f5; }
    code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    pre { background: #000; color: #0f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>