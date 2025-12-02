<?php
// Ensure this path is correct relative to the file's location
include_once('../includes/config.php'); 
require_login(); // Security check using the function from config.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Careers - Admin | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../admin_styles.css"> </head>
<body>
    <div id="admin-sidebar">... Careers Link Here ...</div>
    <div id="admin-content">
        <h2>Career Postings Management</h2>
        
        <?php display_success_message(); ?>
        <?php display_error_message(); ?>

        <button id="add-job-btn" class="btn btn-primary mb-3">Post New Job</button>
        
        <table id="careers-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Dept.</th>
                    <th>Type</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Views/Apps</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="7">Job list placeholder (load with PHP/AJAX in production)</td></tr>
            </tbody>
        </table>

        <div id="job-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <form id="career-form" action="process_careers.php" method="POST">
                    <h3>Post/Edit Job</h3>
                    <input type="hidden" name="action" id="form-action" value="add">
                    <input type="hidden" name="job_id" id="job-id">
                    
                    <label>Job Title:</label><input type="text" name="job_title" class="form-control" required><br>
                    
                    <label>Department:</label>
                    <select name="department" class="form-control" required>
                        <option value="Sales & Marketing">Sales & Marketing</option>
                        <option value="Customer Service">Customer Service</option>
                        <option value="Finance">Finance</option>
                        <option value="HR">HR</option>
                        <option value="Technology">Technology</option>
                    </select><br>

                    <label>Application Deadline:</label><input type="date" name="application_deadline" class="form-control" required><br>
                    
                    <label>Status:</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="closed">Closed</option>
                    </select><br>

                    <button type="submit" class="btn btn-success">Post Job</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('job-modal').style.display='none'">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Basic modal toggle logic...
    </script>
</body>
</html>