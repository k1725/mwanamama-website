# 💼 Career Opportunities Setup Guide

## 📁 Files Created

### Backend Files:
1. `database_careers.sql` - Database tables
2. `backend/api/careers.php` - Careers API
3. `backend/admin/manage_careers.php` - Admin page
4. `backend/admin/process_careers.php` - Backend processing

### Frontend Files:
5. `html-partials/careers.html` - Careers section
6. `js/careers-handler.js` - JavaScript handler

## 🚀 Setup Steps

### Step 1: Create Database Tables
Run the SQL file in phpMyAdmin:
```sql
-- Import database_careers.sql
```

This creates:
- `careers` table - Job postings
- `job_applications` table - Applications (for future)
- Sample job postings (4 jobs)

### Step 2: Add API File
```
backend/api/careers.php
```

### Step 3: Add Admin Files
```
backend/admin/manage_careers.php
backend/admin/process_careers.php
```

### Step 4: Add Frontend Files
```
html-partials/careers.html
js/careers-handler.js
```

### Step 5: Include in Your Main HTML
Add to your index.html or main page:

```html
<!-- Include careers section where you want it -->
<div id="careers-section"></div>

<!-- Add script before closing body tag -->
<script src="js/careers-handler.js"></script>
```

Or if using PHP includes:
```php
<?php include 'html-partials/careers.html'; ?>
```

### Step 6: Test the System

**Test API:**
```
http://localhost/Mwanamama-Website/backend/api/careers.php
```

**Access Admin:**
```
http://localhost/Mwanamama-Website/backend/admin/manage_careers.php
```

**View Frontend:**
```
http://localhost/Mwanamama-Website/index.html#careers
```

## ✨ Features

### Admin Dashboard Features:
- ✅ View all job postings
- ✅ Add new jobs
- ✅ Edit jobs (coming soon)
- ✅ Delete jobs
- ✅ See views and applications count
- ✅ Search jobs
- ✅ Set status (active/closed/draft)
- ✅ Set deadlines

### Frontend Features:
- ✅ Display all active jobs
- ✅ Filter by employment type
- ✅ Job cards with key info
- ✅ Click to view full details
- ✅ Modal with complete job info
- ✅ Deadline countdown
- ✅ Apply button
- ✅ Load more functionality
- ✅ Responsive design

## 🎨 Job Information Included:

- Job Title
- Department
- Location
- Employment Type (Full-time, Part-time, Contract, Internship)
- Experience Level (Entry, Mid, Senior, Executive)
- Description
- Responsibilities
- Requirements
- Benefits
- Salary Range
- Application Deadline
- Views & Applications count

## 📊 Sample Jobs Included:

1. **Business Development Officer** - Sales & Marketing
2. **Customer Service Representative** - Customer Service
3. **Accountant** - Finance
4. **Marketing Intern** - Sales & Marketing (Internship)

## 🎯 How to Use

### Adding a New Job:
1. Go to Admin Dashboard
2. Click "Careers" in sidebar
3. Click "Post New Job"
4. Fill in job details
5. Set status and deadline
6. Click "Post Job"

### Managing Jobs:
- **Active** - Visible on frontend
- **Draft** - Hidden, work in progress
- **Closed** - Position filled

### Frontend Display:
Jobs automatically load from database:
- Only "active" jobs shown
- Sorted by newest first
- Filter by employment type
- Click card to see full details

## 🔧 Customization

### Change Colors:
In `careers.html` CSS:
```css
.filter-btn {
    border: 2px solid #4F46E5; /* Your color */
}
```

### Change Number of Jobs:
In `careers-handler.js`:
```javascript
loadCareers(6); // Change to show more/less
```

### Add Custom Departments:
In `manage_careers.php` modal:
```html
<option value="Your Department">Your Department</option>
```

### Customize Application Process:
In `careers-handler.js`, edit `applyForJob()` function:
```javascript
function applyForJob(careerId) {
    // Redirect to your application form
    window.location.href = `apply.php?job=${careerId}`;
}
```

## 📱 Mobile Responsive

- Cards stack on mobile
- Filter buttons wrap
- Modal scrolls on small screens
- Touch-friendly buttons

## 🔒 Security

- ✅ SQL injection protection
- ✅ XSS prevention (HTML escaping)
- ✅ Session authentication
- ✅ Input validation

## 💡 Future Enhancements

You can add:
- [ ] Online application form
- [ ] Resume upload
- [ ] Application tracking
- [ ] Email notifications
- [ ] Share job postings
- [ ] Job alerts subscription
- [ ] PDF job descriptions
- [ ] Social media sharing

## 🐛 Troubleshooting

### Jobs not showing:
- Check database has jobs with `status = 'active'`
- Test API: `http://localhost/.../backend/api/careers.php`
- Check browser console for errors

### Can't add jobs:
- Verify database tables exist
- Check `process_careers.php` permissions
- Ensure admin is logged in

### Deadline not showing:
- Make sure deadline date is set in database
- Check date format: YYYY-MM-DD

## ✅ Checklist

Before going live:
- [ ] Database tables created
- [ ] API returns JSON
- [ ] Admin page accessible
- [ ] Can add/delete jobs
- [ ] Frontend displays jobs
- [ ] Modals open correctly
- [ ] Filters work
- [ ] Mobile responsive
- [ ] Update email in apply function
- [ ] Test all links
- [ ] Add real job postings

---

**You're all set! Your career opportunities section is ready!** 💼🎉