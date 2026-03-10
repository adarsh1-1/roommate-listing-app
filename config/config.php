<?php
// Configuration file for RoomMate App
// Modify these settings to customize your application

// ===== Application Settings =====
define('APP_NAME', 'RoomMate');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost:8000');
define('APP_DEBUG', true); // Set to false in production

// ===== Database Settings =====
// Uncomment to use database
// define('DB_ENABLED', true);
define('DB_ENABLED', false); // Using mock data for now
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'roommate_db');
define('DB_CHARSET', 'utf8mb4');

// ===== File Upload Settings =====
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// ===== Session Settings =====
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_NAME', 'roommate_session');

// ===== Email Settings (for future use) =====
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('ADMIN_EMAIL', 'admin@roommate.com');

// ===== API Settings =====
define('API_ENABLED', true);
define('API_RATE_LIMIT', 100); // requests per hour
define('API_VERSION', 'v1');

// ===== Pagination Settings =====
define('ITEMS_PER_PAGE', 12);
define('MAX_LISTINGS', 100);

// ===== Currency Settings =====
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');

// ===== Location Settings =====
define('DEFAULT_LOCATION', 'Mumbai');
$ROOM_TYPES = ['Studio', '1 Bed', '2 Bed', '3 Bed', '4 Bed'];
$FURNISHING_TYPES = ['Unfurnished', 'Semi-Furnished', 'Furnished'];

// ===== Amenities List =====
$AMENITIES = [
    'WiFi',
    'AC',
    'Washing Machine',
    'Refrigerator',
    'Parking',
    'Security',
    'Gym',
    'Common Hall',
    'CCTV',
    'Power Backup',
    'Water Filter',
    'Balcony',
    'Garden',
    'Swimming Pool',
    'Elevator',
    'Fire Safety',
    'Hot Water',
    'Sofa',
    'TV',
    'Microwave'
];

// ===== Feature Flags =====
$FEATURES = [
    'search_enabled' => true,
    'advanced_filters' => false,
    'wishlist_enabled' => true,
    'reviews_enabled' => false,
    'ratings_enabled' => true,
    'messaging_enabled' => false,
    'payment_enabled' => false,
    'admin_panel' => false
];

// ===== Social Media Links =====
$SOCIAL_LINKS = [
    'facebook' => 'https://facebook.com/roommate',
    'instagram' => 'https://instagram.com/roommate',
    'twitter' => 'https://twitter.com/roommate',
    'linkedin' => 'https://linkedin.com/company/roommate'
];

// ===== Error Messages =====
$ERROR_MESSAGES = [
    'invalid_email' => 'Please enter a valid email address',
    'password_short' => 'Password must be at least 6 characters',
    'password_mismatch' => 'Passwords do not match',
    'file_too_large' => 'File size must be less than 5MB',
    'invalid_file_type' => 'Invalid file type. Only images allowed',
    'empty_field' => 'This field cannot be empty',
    'user_exists' => 'Email already registered',
    'invalid_credentials' => 'Invalid email or password'
];

// ===== Success Messages =====
$SUCCESS_MESSAGES = [
    'login_success' => 'Welcome back!',
    'signup_success' => 'Account created successfully',
    'listing_added' => 'Your listing has been published',
    'listing_updated' => 'Listing updated successfully',
    'listing_deleted' => 'Listing removed',
    'profile_updated' => 'Profile updated successfully'
];

// ===== Color Theme (Tailwind compatible) =====
$THEME = [
    'primary' => 'purple',
    'secondary' => 'pink',
    'primary_light' => '#667eea',
    'primary_dark' => '#764ba2',
    'secondary_light' => '#ec4899',
    'success' => '#10b981',
    'error' => '#ef4444',
    'warning' => '#f59e0b'
];

// ===== Helper Functions =====

/**
 * Get configuration value
 */
function get_config($key, $default = null) {
    global $AMENITIES, $FEATURES, $ROOM_TYPES, $FURNISHING_TYPES, $THEME;
    
    $config = [
        'amenities' => $AMENITIES,
        'features' => $FEATURES,
        'room_types' => $ROOM_TYPES,
        'furnishing' => $FURNISHING_TYPES,
        'theme' => $THEME
    ];
    
    return isset($config[$key]) ? $config[$key] : $default;
}

/**
 * Check if feature is enabled
 */
function is_feature_enabled($feature) {
    global $FEATURES;
    return isset($FEATURES[$feature]) && $FEATURES[$feature] === true;
}

/**
 * Format price with currency
 */
function format_price($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 0);
}

/**
 * Get error message
 */
function get_error_msg($key) {
    global $ERROR_MESSAGES;
    return isset($ERROR_MESSAGES[$key]) ? $ERROR_MESSAGES[$key] : 'An error occurred';
}

/**
 * Get success message
 */
function get_success_msg($key) {
    global $SUCCESS_MESSAGES;
    return isset($SUCCESS_MESSAGES[$key]) ? $SUCCESS_MESSAGES[$key] : 'Operation successful';
}

?>
