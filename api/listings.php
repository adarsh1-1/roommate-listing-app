<?php
/**
 * Listings API
 * 
 * Endpoints:
 * GET /api/listings.php?action=get_listings - Get all listings
 * GET /api/listings.php?action=get_listing&id=1 - Get single listing
 * GET /api/listings.php?action=search - Search listings
 * POST /api/listings.php?action=create - Create new listing (requires auth)
 * POST /api/listings.php?action=update - Update listing (requires auth)
 * POST /api/listings.php?action=delete - Delete listing (requires auth)
 * GET /api/listings.php?action=owner_listings - Get owner's listings (requires auth)
 */

header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../config/aws-sdk.php';

// Get CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_listings':
            get_listings();
            break;
            
        case 'get_listing':
            get_listing();
            break;
            
        case 'search':
            search_listings();
            break;
            
        case 'create':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            create_listing();
            break;
            
        case 'update':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            update_listing();
            break;
            
        case 'delete':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            delete_listing();
            break;
            
        case 'owner_listings':
            if ($method !== 'GET') {
                throw new Exception('Invalid request method');
            }
            owner_listings();
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Get all active listings
 */
function get_listings() {
    $page = $_GET['page'] ?? 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    try {
        // Get total count
        $countResult = getRow(
            "SELECT COUNT(*) as total FROM pg_listings WHERE is_active = TRUE"
        );
        $total = $countResult['total'] ?? 0;
        
        // Get listings
        $listings = getAllRows(
            "SELECT id, title, description, rent, location, amenities, 
                    available_rooms, furnishing, image_url, created_at, 
                    (SELECT name FROM users WHERE id = owner_id) as owner_name
             FROM pg_listings 
             WHERE is_active = TRUE 
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset],
            "ii"
        );
        
        // Parse JSON fields
        $listings = array_map(function($listing) {
            if (!empty($listing['amenities'])) {
                $listing['amenities'] = json_decode($listing['amenities'], true);
            }
            return $listing;
        }, $listings);
        
        echo json_encode([
            'success' => true,
            'listings' => $listings,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Get single listing
 */
function get_listing() {
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid listing ID']);
        return;
    }
    
    try {
        // Get main listing
        $listing = getRow(
            "SELECT pl.*, u.name as owner_name, u.phone as owner_phone, u.email as owner_email
             FROM pg_listings pl
             JOIN users u ON pl.owner_id = u.id
             WHERE pl.id = ? AND pl.is_active = TRUE",
            [$id],
            "i"
        );
        
        if (!$listing) {
            http_response_code(404);
            echo json_encode(['error' => 'Listing not found']);
            return;
        }
        
        // Get all images for this listing
        $images = getAllRows(
            "SELECT image_url, s3_key, is_primary FROM listing_images WHERE listing_id = ? ORDER BY is_primary DESC",
            [$id],
            "i"
        );
        
        // Parse JSON
        if (!empty($listing['amenities'])) {
            $listing['amenities'] = json_decode($listing['amenities'], true);
        }
        
        $listing['images'] = $images;
        
        echo json_encode([
            'success' => true,
            'listing' => $listing
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Search listings
 */
function search_listings() {
    $query = $_GET['q'] ?? '';
    $location = $_GET['location'] ?? '';
    $max_rent = intval($_GET['max_rent'] ?? 0);
    $min_rent = intval($_GET['min_rent'] ?? 0);
    $page = intval($_GET['page'] ?? 1);
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    try {
        $whereConditions = ["pl.is_active = TRUE"];
        $params = [];
        $types = "";
        
        if (!empty($location)) {
            $whereConditions[] = "pl.location LIKE ?";
            $params[] = "%$location%";
            $types .= "s";
        }
        
        if (!empty($query)) {
            $whereConditions[] = "MATCH(pl.title, pl.description) AGAINST(?)";
            $params[] = $query;
            $types .= "s";
        }
        
        if ($max_rent > 0) {
            $whereConditions[] = "pl.rent <= ?";
            $params[] = $max_rent;
            $types .= "i";
        }
        
        if ($min_rent > 0) {
            $whereConditions[] = "pl.rent >= ?";
            $params[] = $min_rent;
            $types .= "i";
        }
        
        $where = implode(" AND ", $whereConditions);
        
        // Get count
        $countQuery = "SELECT COUNT(*) as total FROM pg_listings pl WHERE $where";
        $countParams = $params;
        $countTypes = $types;
        
        $countResult = getRow($countQuery, $countParams, $countTypes);
        $total = $countResult['total'] ?? 0;
        
        // Get listings
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
        
        $listingsQuery = "SELECT pl.id, pl.title, pl.rent, pl.location, 
                         pl.image_url, pl.furnishing, (SELECT name FROM users WHERE id = pl.owner_id) as owner_name
                         FROM pg_listings pl
                         WHERE $where
                         ORDER BY pl.created_at DESC
                         LIMIT ? OFFSET ?";
        
        $listings = getAllRows($listingsQuery, $params, $types);
        
        echo json_encode([
            'success' => true,
            'listings' => $listings,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Create new listing
 */
function create_listing() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }
    
    if ($_SESSION['user_role'] !== 'owner') {
        http_response_code(403);
        echo json_encode(['error' => 'Only owners can create listings']);
        return;
    }
    
    try {
        global $awsManager;
        
        $input = getInputData();
        
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $rent = intval($input['rent'] ?? 0);
        $location = trim($input['location'] ?? '');
        $amenities = $input['amenities'] ?? [];
        $available_rooms = intval($input['available_rooms'] ?? 1);
        $furnishing = $input['furnishing'] ?? 'semi-furnished';
        $area = trim($input['area'] ?? '');
        
        // Validation
        $errors = [];
        if (empty($title)) $errors[] = 'Title required';
        if (empty($description)) $errors[] = 'Description required';
        if ($rent <= 0) $errors[] = 'Valid rent required';
        if (empty($location)) $errors[] = 'Location required';
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return;
        }
        
        // Create listing
        $listing_id = insertRecord(
            "INSERT INTO pg_listings (owner_id, title, description, rent, location, amenities, available_rooms, furnishing, area)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $_SESSION['user_id'],
                $title,
                $description,
                $rent,
                $location,
                json_encode($amenities),
                $available_rooms,
                $furnishing,
                $area
            ],
            "issisisss"
        );
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $awsManager->uploadFromFilesArray($_FILES['image']);
            
            if ($uploadResult['success']) {
                // Save image info to database
                insertRecord(
                    "INSERT INTO listing_images (listing_id, image_url, s3_key, is_primary) VALUES (?, ?, ?, TRUE)",
                    [$listing_id, $uploadResult['url'], $uploadResult['key']],
                    "iss"
                );
                
                // Update pg_listings with primary image
                updateRecord(
                    "UPDATE pg_listings SET image_url = ? WHERE id = ?",
                    [$uploadResult['url'], $listing_id],
                    "si"
                );
            }
        }
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Listing created successfully',
            'listing_id' => $listing_id
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Get input data from JSON or POST
 */
function getInputData() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') === 0) {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
    return $_POST;
}

/**
 * Update listing
 */
function update_listing() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }
    
    try {
        $input = getInputData();
        $listing_id = intval($input['id'] ?? 0);
        
        if ($listing_id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid listing ID']);
            return;
        }
        
        // Check if listing exists and user is owner
        $listing = getRow(
            "SELECT owner_id FROM pg_listings WHERE id = ?",
            [$listing_id],
            "i"
        );
        
        if (!$listing) {
            http_response_code(404);
            echo json_encode(['error' => 'Listing not found']);
            return;
        }
        
        if ($listing['owner_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'You cannot edit this listing']);
            return;
        }
        
        // Update fields
        $updates = [];
        $params = [];
        $types = "";
        
        if (isset($input['title'])) {
            $updates[] = "title = ?";
            $params[] = trim($input['title']);
            $types .= "s";
        }
        if (isset($input['description'])) {
            $updates[] = "description = ?";
            $params[] = trim($input['description']);
            $types .= "s";
        }
        if (isset($input['rent'])) {
            $updates[] = "rent = ?";
            $params[] = intval($input['rent']);
            $types .= "i";
        }
        if (isset($input['location'])) {
            $updates[] = "location = ?";
            $params[] = trim($input['location']);
            $types .= "s";
        }
        if (isset($input['amenities'])) {
            $updates[] = "amenities = ?";
            $params[] = json_encode($input['amenities']);
            $types .= "s";
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        $params[] = $listing_id;
        $types .= "i";
        
        $updateQuery = "UPDATE pg_listings SET " . implode(", ", $updates) . " WHERE id = ?";
        updateRecord($updateQuery, $params, $types);
        
        echo json_encode([
            'success' => true,
            'message' => 'Listing updated successfully'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Delete listing
 */
function delete_listing() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }
    
    try {
        global $awsManager;
        
        $input = getInputData();
        $listing_id = intval($input['id'] ?? 0);
        
        if ($listing_id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid listing ID']);
            return;
        }
        
        // Check if listing exists and user is owner
        $listing = getRow(
            "SELECT owner_id FROM pg_listings WHERE id = ?",
            [$listing_id],
            "i"
        );
        
        if (!$listing) {
            http_response_code(404);
            echo json_encode(['error' => 'Listing not found']);
            return;
        }
        
        if ($listing['owner_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'You cannot delete this listing']);
            return;
        }
        
        // Delete images from S3
        $images = getAllRows(
            "SELECT s3_key FROM listing_images WHERE listing_id = ?",
            [$listing_id],
            "i"
        );
        
        foreach ($images as $image) {
            $awsManager->deleteFromS3($image['s3_key']);
        }
        
        // Delete listing (cascade will delete images)
        updateRecord(
            "DELETE FROM pg_listings WHERE id = ?",
            [$listing_id],
            "i"
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Get owner's listings
 */
function owner_listings() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }
    
    try {
        $listings = getAllRows(
            "SELECT id, title, rent, location, image_url, is_active, created_at 
             FROM pg_listings 
             WHERE owner_id = ? 
             ORDER BY created_at DESC",
            [$_SESSION['user_id']],
            "i"
        );
        
        echo json_encode([
            'success' => true,
            'listings' => $listings
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

?>
