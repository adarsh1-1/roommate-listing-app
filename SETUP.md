# Roommate Listing App - Setup Guide

## 🚀 Quick Start Guide

### Step 1: Install PHP Server
The application requires a PHP server to run. Choose one:

#### Option A: Using XAMPP (Recommended for Windows)
1. Download XAMPP from https://www.apachefriends.org/
2. Install and start Apache
3. Place project in `C:\xampp\htdocs\roommate-listing-app\`
4. Access at `http://localhost/roommate-listing-app/`

#### Option B: Using PHP Built-in Server (Quick Testing)
```bash
cd roommate-listing-app
php -S localhost:8000
```
Then open `http://localhost:8000/` in your browser

### Step 2: Directory Setup
Make sure the `uploads/` directory has write permissions:

**Windows:**
```bash
icacls uploads /grant Users:M
```

**Linux/Mac:**
```bash
chmod 755 uploads/
```

### Step 3: Configuration
The app works out-of-the-box with mock data. To integrate with a database:

1. Edit `config/database.php`
2. Update credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'roommate_db');
   ```

### Database Schema (Optional)

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    rent INT NOT NULL,
    deposit INT,
    room_type VARCHAR(50) NOT NULL,
    furnishing VARCHAR(50),
    area VARCHAR(50),
    image_url VARCHAR(255),
    description TEXT,
    rating DECIMAL(3,1),
    reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE amenities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    name VARCHAR(100),
    FOREIGN KEY (listing_id) REFERENCES listings(id)
);
```

## 📱 Pages Overview

- **`/`** - Landing page with search and listings
- `?page=details&id=1` - Listing details
- `?page=login` - Login
- `?page=signup` - Sign up
- `?page=add-listing` - Create new listing

## 🎨 Customizing Design

All styling uses **Tailwind CSS** via CDN. To customize:

1. **Colors**: Modify gradient in `index.php` style tag
2. **Spacing**: Adjust `py-`, `px-`, `mb-` classes
3. **Fonts**: Add custom fonts in `<head>` section

## 🔧 Troubleshooting

### Blank Page?
- Check PHP is installed: `php -v`
- Check web server is running
- Check error logs in browser console (F12)

### Images Not Uploading?
- Check `uploads/` folder permissions
- Ensure file size < 5MB
- Verify allowed file types in `add-listing.php`

### Database Connection Error?
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database exists

## 💡 Tips

- Use Firefox or Chrome DevTools to test responsive design
- Disable JavaScript to test form fallbacks
- Check console for any errors (F12 > Console)

---

**Happy listing! 🏠**
