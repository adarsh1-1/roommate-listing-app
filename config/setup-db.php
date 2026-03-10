<?php
/**
 * Database Schema Setup
 * 
 * This file initializes the database schema for the PG Listing Platform.
 * Run this once to set up the required tables in AWS RDS.
 * 
 * ACCESS: http://yoursite.com/config/setup-db.php
 */

require_once 'database.php';

// Set headers for output
header('Content-Type: application/json');

// Check if setup is already done
$setupFlag = getenv('DB_SETUP_COMPLETE');

if ($_GET['action'] ?? '' !== 'setup') {
    echo json_encode([
        'status' => 'ready',
        'message' => 'Database setup utility ready',
        'action' => 'POST to this script with action=setup to initialize database',
        'command' => 'curl -X POST "http://yoursite.com/config/setup-db.php?action=setup"'
    ]);
    exit;
}

try {
    $conn = getDbConnection();
    
    // 1. Create users table
    $users_table = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        role ENUM('user', 'owner') DEFAULT 'user',
        is_verified BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_email (email),
        INDEX idx_role (role)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // 2. Create pg_listings table
    $listings_table = "
    CREATE TABLE IF NOT EXISTS pg_listings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description LONGTEXT,
        rent INT NOT NULL,
        location VARCHAR(255) NOT NULL,
        amenities JSON,
        available_rooms INT DEFAULT 1,
        furnishing ENUM('unfurnished', 'semi-furnished', 'furnished') DEFAULT 'semi-furnished',
        area VARCHAR(100),
        image_url VARCHAR(500),
        s3_key VARCHAR(500),
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_owner_id (owner_id),
        INDEX idx_location (location),
        INDEX idx_rent (rent),
        INDEX idx_active (is_active),
        FULLTEXT INDEX ft_title_desc (title, description)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // 3. Create listing_images table for multiple images per listing
    $images_table = "
    CREATE TABLE IF NOT EXISTS listing_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        image_url VARCHAR(500) NOT NULL,
        s3_key VARCHAR(500) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (listing_id) REFERENCES pg_listings(id) ON DELETE CASCADE,
        INDEX idx_listing_id (listing_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // 4. Create search_preferences table (optional - for saved searches)
    $search_table = "
    CREATE TABLE IF NOT EXISTS search_preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        location VARCHAR(255),
        max_rent INT,
        min_rent INT,
        date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // 5. Create bookmarks/favorites table
    $favorites_table = "
    CREATE TABLE IF NOT EXISTS user_favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        listing_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_favorite (user_id, listing_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (listing_id) REFERENCES pg_listings(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // 6. Create inquiries table for user inquiries
    $inquiries_table = "
    CREATE TABLE IF NOT EXISTS inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        user_id INT NOT NULL,
        message TEXT,
        phone VARCHAR(20),
        status ENUM('new', 'viewed', 'responded', 'closed') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (listing_id) REFERENCES pg_listings(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_listing_id (listing_id),
        INDEX idx_user_id (user_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // Execute all table creations
    $tables = [
        'users' => $users_table,
        'pg_listings' => $listings_table,
        'listing_images' => $images_table,
        'search_preferences' => $search_table,
        'user_favorites' => $favorites_table,
        'inquiries' => $inquiries_table
    ];
    
    $created_tables = [];
    $errors = [];
    
    foreach ($tables as $table_name => $sql) {
        try {
            if ($conn->query($sql) === TRUE) {
                $created_tables[] = $table_name;
            } else {
                $errors[] = "$table_name: " . $conn->error;
            }
        } catch (Exception $e) {
            $errors[] = "$table_name: " . $e->getMessage();
        }
    }
    
    // Create response
    $response = [
        'status' => 'success',
        'message' => 'Database tables created successfully',
        'tables_created' => $created_tables,
        'tables_count' => count($created_tables)
    ];
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
