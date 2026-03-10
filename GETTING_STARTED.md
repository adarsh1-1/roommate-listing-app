# 🚀 Getting Started with RoomMate

## Quick Setup (2 minutes)

### Option 1: Using PHP Built-in Server (Easiest)

```bash
# Navigate to project directory
cd c:\Users\adars\git\roommate-listing-app

# Start PHP server
php -S localhost:8000

# Open browser
start http://localhost:8000
```

**Done!** The app is running.

---

### Option 2: Using XAMPP (Recommended)

1. **Install XAMPP**: https://www.apachefriends.org/
2. **Copy project**:
   ```
   C:\xampp\htdocs\roommate-listing-app\
   ```
3. **Start Apache** in XAMPP Control Panel
4. **Open browser**: `http://localhost/roommate-listing-app/`

---

### Option 3: Using WAMP

1. **Install WAMP**: http://www.wampserver.com/
2. **Copy project**:
   ```
   C:\wamp\www\roommate-listing-app\
   ```
3. **Start WAMP**
4. **Open browser**: `http://localhost/roommate-listing-app/`

---

## 📱 Test the App

### Navigation Flow to Test

1. **Landing Page** `http://localhost:8000/`
   - View featured listings
   - See search bar
   - Click "View Details" on any card

2. **Listing Details** `http://localhost:8000/?page=details&id=1`
   - Browse image gallery (click thumbnails)
   - View amenities
   - Check contact info
   - Try WhatsApp/Call buttons

3. **Add Listing** `http://localhost:8000/?page=add-listing`
   - Try drag-drop image upload
   - Fill form fields
   - Test form validation
   - Submit (will show success message)

4. **Login** `http://localhost:8000/?page=login`
   - Enter any email/password
   - Test social login buttons

5. **Sign Up** `http://localhost:8000/?page=signup`
   - Test form validation
   - Try password confirmation

---

## 📂 Project Structure Explained

```
roommate-listing-app/
│
├── 📄 index.php              ← Main file (start here!)
│                              Handles all routing
│
├── 📄 logout.php             ← Logout handler
│
├── 📁 pages/                 ← All page templates
│   ├── landing.php           ← Homepage with listings
│   ├── listing-details.php   ← Property details
│   ├── login.php             ← Login form
│   ├── signup.php            ← Registration form
│   └── add-listing.php       ← Create listing
│
├── 📁 config/                ← Configuration
│   ├── database.php          ← DB connection
│   └── config.php            ← App settings
│
├── 📁 api/                   ← API endpoints
│   └── listings.php          ← API example
│
├── 📁 uploads/               ← Upload directory
│   └── .gitkeep              ← Placeholder
│
├── 📄 README.md              ← Full docs
├── 📄 SETUP.md               ← Setup guide
├── 📄 FEATURES.md            ← Feature overview
└── 📄 PROJECT_SUMMARY.md     ← Project summary
```

---

## 🎨 How Pages Work

### Main Router (`index.php`)
- Checks `?page=` parameter
- Loads corresponding page file
- Includes navigation and footer on all pages
- Handles user sessions

### Page URL Pattern
```
http://localhost:8000/?page=PAGE_NAME
```

| Page | URL | File |
|------|-----|------|
| Landing | `/` or `?page=landing` | `pages/landing.php` |
| Details | `?page=details&id=1` | `pages/listing-details.php` |
| Login | `?page=login` | `pages/login.php` |
| Sign Up | `?page=signup` | `pages/signup.php` |
| Add Listing | `?page=add-listing` | `pages/add-listing.php` |

---

## 🔧 Key Customizations

### 1. Change Colors
**File**: `index.php` (lines 12-16)
```php
.gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### 2. Update Site Name
**File**: `index.php` (line 31)
```php
<a href="index.php?page=landing" class="text-2xl font-bold ...">
    RoomMate  ← Change this
</a>
```

### 3. Modify Listings
**File**: `pages/landing.php` (lines 10-37)
Edit the `$listings` array with your data

### 4. Update Amenities
**File**: `pages/listing-details.php` (lines 38-53)
Edit the `$amenities` array

### 5. Change Form Fields
**File**: `pages/add-listing.php`
Add or remove form input fields

---

## 📝 Sample Data Included

### Listings (6 pre-loaded)
- 2BHK in Bandra (₹45,000)
- 1BHK in Andheri (₹32,000)
- Studio in Colaba (₹28,000)
- 3BHK in Powai (₹85,000)
- 2BHK in Thane (₹38,000)
- 1BHK in Navi Mumbai (₹35,000)

All use Unsplash images for demonstration.

---

## 🗄️ Database Setup (Optional)

If you want to use a real database instead of mock data:

### 1. Create Database
```sql
CREATE DATABASE roommate_db;
USE roommate_db;

-- Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Listings Table
CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    rent INT NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 2. Update Configuration
**File**: `config/database.php`
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'roommate_db');
```

### 3. Update Queries
Replace sample data arrays with database queries in each page file.

---

## 🐛 Troubleshooting

### Problem: Blank Page
**Solution**:
- Check PHP is installed: `php -v`
- Check web server is running
- Check browser console for errors (F12)
- Check PHP error logs

### Problem: Can't Access localhost:8000
**Solution**:
- Stop any other server on port 8000
- Try different port: `php -S localhost:8001`
- Check firewall settings

### Problem: Images Not Loading
**Solution**:
- Check internet (using Unsplash URLs)
- Update URLs in `pages/landing.php`
- Or upload your own images to `uploads/` folder

### Problem: Form Submission Error
**Solution**:
- Check `uploads/` folder permissions
- Ensure folder is writable by web server
- Check uploaded file size (max 5MB)

### Problem: Session Not Working
**Solution**:
- Check if cookies are enabled
- Verify PHP session.save_path is writable
- Check browser privacy settings

---

## 📚 Code Examples

### Add a New Listing (in landing.php)
```php
$listings[] = [
    'id' => 7,
    'title' => 'Your Title Here',
    'location' => 'Location',
    'rent' => '₹40,000',
    'room_type' => '2 Bed',
    'image' => 'https://image-url.jpg',
    'rating' => 4.5,
    'reviews' => 10
];
```

### Use Configuration
```php
require_once 'config/config.php';

// Format price
echo format_price(45000); // Output: ₹45,000

// Check feature
if(is_feature_enabled('wishlist_enabled')) {
    // Show wishlist button
}

// Get amenities
$amenities = get_config('amenities');
```

### Check User Session
```php
if(isset($_SESSION['user_id'])) {
    echo "Welcome " . $_SESSION['user_name'];
} else {
    echo "Please login";
}
```

---

## 🎓 Learning Resources

- **PHP Basics**: https://www.php.net/manual/
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Font Awesome**: https://fontawesome.com/docs
- **HTML Forms**: https://developer.mozilla.org/en-US/docs/Learn/Forms

---

## ✅ Verification Checklist

- [ ] Server is running (PHP built-in or XAMPP)
- [ ] Can access http://localhost:8000/
- [ ] Landing page shows 6 listings
- [ ] Can navigate to details page
- [ ] Can view login/signup forms
- [ ] Can open add listing page
- [ ] Image gallery works
- [ ] Forms have validation
- [ ] Contact buttons work
- [ ] Search bar is visible

---

## 🚀 Next Steps

1. **Customize Design**
   - Update colors and fonts
   - Change logo and branding
   - Adjust spacing and layout

2. **Add Content**
   - Replace sample listings
   - Update amenities
   - Customize descriptions

3. **Set Up Database**
   - Create MySQL database
   - Update connection settings
   - Migrate sample data

4. **Deploy**
   - Choose hosting provider
   - Upload files via FTP
   - Configure domain
   - Set up SSL certificate

---

## 💬 Support

If you need help:
1. Check the code comments
2. Review the documentation files
3. Check browser console (F12)
4. Review PHP error logs

---

**You're all set! Happy listing! 🎉**
