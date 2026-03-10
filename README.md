# RoomMate - Modern Roommate/PG Listing Web App

A modern, fully responsive web application for finding and listing roommate and PG accommodations. Built with PHP and Tailwind CSS with a clean, minimalist SaaS-like design.

## Features

### 🏠 Pages Included

1. **Landing Page**
   - Hero section with gradient background
   - Advanced search bar with filters
   - Featured listings in card format
   - Each card displays: image, rent, location, room type, ratings, and view button
   - Fully responsive grid layout

2. **Listing Details Page**
   - Image gallery with thumbnail navigation
   - Complete property details (rent, deposit, area, furnishing)
   - Amenities checklist
   - Detailed description
   - Owner contact card with call, WhatsApp, and email buttons
   - Save to favorites functionality
   - Safety tips

3. **Authentication Pages**
   - Login page with form validation
   - Signup page with email and phone verification
   - Social login options (UI ready)
   - Password confirmation
   - Terms & conditions acceptance

4. **Add Listing Page**
   - Image upload with drag-and-drop support
   - Form validation for all fields
   - Image preview before upload
   - Room type and furnishing selection
   - Detailed description textarea
   - Responsive form layout

## 🎨 Design Features

- **Modern Gradient Theme**: Purple to pink gradient for primary actions
- **Clean Card Layouts**: Soft shadows and rounded edges (24px borders)
- **Hover Effects**: Smooth transitions and elevations on interaction
- **Responsive Design**: Mobile-first approach, works perfectly on all screen sizes
- **Minimalist Aesthetic**: SaaS-like clean design with plenty of whitespace
- **Accessibility**: Semantic HTML and proper form labels

## 📁 Project Structure

```
roommate-listing-app/
├── index.php                 # Main entry point with routing
├── logout.php               # Session logout
├── config/
│   └── database.php         # Database configuration
├── pages/
│   ├── landing.php          # Landing/homepage with listings
│   ├── listing-details.php  # Property details page
│   ├── login.php            # Login page
│   ├── signup.php           # Sign up page
│   └── add-listing.php      # Add new listing page
├── api/                     # API endpoints (for future use)
└── uploads/                 # Directory for uploaded images
```

## 🚀 Getting Started

### Requirements
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation

1. **Clone or download the project** to your web server directory (htdocs for XAMPP, www for WAMP)

2. **Configure Database** (Optional - app works with mock data):
   - Edit `config/database.php` with your database credentials
   - Create database and tables as needed

3. **Set Permissions**:
   ```bash
   chmod 755 uploads/
   ```

4. **Access the Application**:
   - Open `http://localhost/roommate-listing-app/` in your browser

## 🛠️ Technology Stack

- **Backend**: PHP
- **Frontend**: HTML5, Tailwind CSS
- **Icons**: Font Awesome 6.4.0
- **JavaScript**: Vanilla JS for interactivity

## 🎯 Key Features

### Landing Page
- Search bar with location and room type filters
- Grid layout with 6 featured listings
- Smooth card hover animations
- Rating and review display
- Quick action buttons

### Listing Details
- Full-screen image gallery with thumbnails
- Complete property information
- Amenities grid with icons
- Owner contact information
- Direct call, WhatsApp, and email integration
- Save listing functionality

### Authentication
- Email and password validation
- Form error handling
- Session management
- Social login UI
- Password confirmation on signup

### Add Listing
- Drag-and-drop image upload
- Form validation
- Image preview
- Multiple furnishing options
- Comprehensive property details form

## 📱 Responsive Breakpoints

- **Mobile**: < 640px (full-width, single column)
- **Tablet**: 640px - 1024px (2-column grid)
- **Desktop**: > 1024px (3-column grid)

## 🔐 Security Notes

- Uses prepared statements for queries
- Input sanitization with `safe_input()` function
- HTML escaping for output
- Session-based authentication
- File upload validation

## 💡 Customization

### Colors
Modify Tailwind CSS gradient in `index.php`:
```html
gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Listings Data
Currently uses sample data in `pages/landing.php`. Connect to database by replacing the array with:
```php
$listings = $conn->query("SELECT * FROM listings")->fetch_all(MYSQLI_ASSOC);
```

### Amenities
Edit the amenities array in `pages/listing-details.php` to match your database schema.

## 🚀 Future Enhancements

- Database integration for persistent data
- User dashboard with saved listings
- Advanced search and filters
- Reviews and ratings system
- Payment integration
- Admin panel
- Email notifications
- Location maps integration

## 📄 License

Open source - feel free to use and modify for your needs.

## 📞 Support

For issues or questions, check the code comments or review the PHP documentation.

---

**Built with ❤️ for modern roommate hunting**
