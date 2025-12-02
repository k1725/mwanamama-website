# 🔌 API Integration Guide - Blog & Gallery

## 📁 Files Created

### Backend API Endpoints:
1. `backend/api/blogs.php` - Blog posts API
2. `backend/api/gallery.php` - Gallery images API

### Frontend JavaScript:
3. `js/api-handler.js` - API fetch and display logic

## 🚀 Setup Instructions

### Step 1: Create API Folder
```bash
mkdir backend/api
```

### Step 2: Upload API Files
Place these files in your backend:
- `backend/api/blogs.php`
- `backend/api/gallery.php`

### Step 3: Update Frontend HTML

Replace your current blog and gallery sections with the updated versions:

#### For Blog Section:
Replace the entire `<section id="blog">` with the content from "Updated Blog Section HTML"

#### For Gallery Section:
Replace the entire `<section id="gallery">` with the content from "Updated Gallery Section HTML"

### Step 4: Add JavaScript File

Add this script tag BEFORE the closing `</body>` tag in your HTML:
```html
<script src="js/api-handler.js"></script>
```

### Step 5: Create Default Images (Optional)

Create fallback images in case uploads fail:
- `images/default-blog.jpg` (600x400px)
- `images/default-gallery.jpg` (600x400px)

## 📡 API Endpoints

### Blogs API
**Endpoint:** `backend/api/blogs.php`

**Get Multiple Blogs:**
```
GET /backend/api/blogs.php?limit=3&offset=0
```

**Get Single Blog:**
```
GET /backend/api/blogs.php?id=1
```

**Parameters:**
- `limit` (optional) - Number of posts to return (default: 10)
- `offset` (optional) - Skip first N posts (default: 0)
- `category` (optional) - Filter by category
- `search` (optional) - Search in title and content
- `id` (optional) - Get specific blog post

**Response Format:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Blog Title",
            "slug": "blog-title",
            "author": "Admin",
            "category": "Business",
            "content": "Full content...",
            "excerpt": "Short excerpt...",
            "image": "../backend/uploads/blog_123.jpg",
            "views": 42,
            "created_at": "2025-10-30 10:00:00",
            "formatted_date": "October 30, 2025"
        }
    ],
    "pagination": {
        "total": 10,
        "limit": 3,
        "offset": 0,
        "has_more": true
    }
}
```

### Gallery API
**Endpoint:** `backend/api/gallery.php`

**Get Multiple Images:**
```
GET /backend/api/gallery.php?limit=6&offset=0
```

**Get Single Image:**
```
GET /backend/api/gallery.php?id=1
```

**Parameters:**
- `limit` (optional) - Number of images to return (default: 12)
- `offset` (optional) - Skip first N images (default: 0)
- `category` (optional) - Filter by category
- `id` (optional) - Get specific image

**Response Format:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Image Title",
            "description": "Image description",
            "category": "Events",
            "image": "../backend/uploads/gallery_123.jpg",
            "views": 15,
            "created_at": "2025-10-30 10:00:00",
            "formatted_date": "October 30, 2025"
        }
    ],
    "pagination": {
        "total": 20,
        "limit": 6,
        "offset": 0,
        "has_more": true
    }
}
```

## 🎨 Frontend Functions

### Available JavaScript Functions:

#### Load Blogs
```javascript
loadBlogs(limit); // Load initial blog posts
```

#### Load Gallery
```javascript
loadGallery(limit); // Load initial gallery images
```

#### Show Blog Modal
```javascript
showBlogModal(blogId); // Open blog post in modal
```

#### Show Gallery Modal
```javascript
showGalleryModal(imageId); // Open gallery image in modal
```

#### Load More
```javascript
loadMoreBlogs(); // Load additional blog posts
loadMoreGallery(); // Load additional gallery images
```

## ✨ Features

### ✅ Blog Features:
- Automatic loading from database
- Dynamic modal creation
- Category badges
- Author and date display
- View counter
- Search and filtering
- Pagination
- Load more functionality
- XSS protection
- Fallback images

### ✅ Gallery Features:
- Automatic loading from database
- Dynamic modal creation
- Zoom overlay effect
- Category organization
- View counter
- Pagination
- Load more functionality
- XSS protection
- Fallback images

## 🔒 Security Features

1. **SQL Injection Protection** - Uses mysqli_real_escape_string
2. **XSS Protection** - HTML escaping on frontend
3. **CORS Headers** - Allows cross-origin requests
4. **Input Validation** - Type checking and sanitization
5. **Error Handling** - Graceful error messages

## 🐛 Troubleshooting

### Issue: API returns empty data
**Solution:** 
- Check if blogs/gallery exist in database
- Verify status is 'published' for blogs
- Verify status is 'active' for gallery
- Check database connection in config.php

### Issue: Images not loading
**Solution:**
- Verify uploads folder exists
- Check image paths in database
- Ensure images are in backend/uploads/
- Add default fallback images

### Issue: CORS errors
**Solution:**
- API already includes CORS headers
- If still issues, check .htaccess or server config

### Issue: Modals not opening
**Solution:**
- Ensure Bootstrap JS is loaded
- Check browser console for errors
- Verify modal HTML is being created

## 📊 Testing the API

### Test Blog API:
Open in browser:
```
http://localhost/your_project/backend/api/blogs.php
```

Should return JSON with blog posts.

### Test Gallery API:
Open in browser:
```
http://localhost/your_project/backend/api/gallery.php
```

Should return JSON with gallery images.

### Test with Parameters:
```
http://localhost/your_project/backend/api/blogs.php?limit=5&category=Business
```

## 📝 Customization

### Change Number of Items Displayed:

In `api-handler.js`, modify:
```javascript
loadBlogs(3);  // Change to your desired number
loadGallery(6); // Change to your desired number
```

### Customize Empty State Message:

In `api-handler.js`, find `showEmptyState()` function and modify messages.

### Add Filtering by Category:

Add category filter buttons and call:
```javascript
fetch(`${API_BASE_URL}blogs.php?category=Business`)
```

## 🎯 Next Steps

1. ✅ Add blogs via admin dashboard
2. ✅ Add gallery images via admin dashboard
3. ✅ Test API endpoints in browser
4. ✅ Verify data displays on frontend
5. ✅ Customize styling as needed
6. ✅ Add more features (search, filters, etc.)

## 💡 Tips

- Keep image sizes consistent for better UI
- Optimize images before uploading
- Use descriptive titles and categories
- Write compelling excerpts
- Test on mobile devices
- Monitor view counts
- Update content regularly

---

**Version:** 1.0.0  
**Last Updated:** October 2025

🌸 **Your blog and gallery are now fully dynamic!**