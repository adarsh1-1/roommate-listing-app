<?php
/**
 * Authentication API
 * 
 * Endpoints:
 * POST /api/auth/register - Register new user
 * POST /api/auth/login - Login user
 * POST /api/auth/logout - Logout user
 * GET /api/auth/user - Get current user info
 */

header('Content-Type: application/json');

require_once '../config/database.php';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// CORS headers (for API consumption)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    switch ($action) {
        case 'register':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            handleRegister();
            break;
            
        case 'login':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            handleLogin();
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        case 'user':
            if ($method !== 'GET') {
                throw new Exception('Invalid request method');
            }
            getCurrentUser();
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
 * Handle user registration
 */
function handleRegister() {
    // Get JSON or POST data
    $input = getInputData();
    
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';
    $phone = trim($input['phone'] ?? '');
    $role = $input['role'] ?? 'user'; // 'user' or 'owner'
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^\d{10}$/', preg_replace('/\D/', '', $phone))) {
        $errors[] = 'Invalid phone number format';
    }
    
    if (!in_array($role, ['user', 'owner'])) {
        $errors[] = 'Invalid role';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
        return;
    }
    
    // Check if email already exists
    $existingUser = getRow(
        "SELECT id FROM users WHERE email = ?",
        [$email],
        "s"
    );
    
    if ($existingUser) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already registered']);
        return;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user into database
    try {
        $userId = insertRecord(
            "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)",
            [$name, $email, $passwordHash, $phone, $role],
            "sssss"
        );
        
        // Start session
        session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => $role
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
    }
}

/**
 * Handle user login
 */
function handleLogin() {
    // Get JSON or POST data
    $input = getInputData();
    
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }
    
    // Get user from database
    try {
        $user = getRow(
            "SELECT id, name, email, password, role FROM users WHERE email = ?",
            [$email],
            "s"
        );
        
        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password']);
            return;
        }
        
        // Update last_login
        updateRecord(
            "UPDATE users SET last_login = NOW() WHERE id = ?",
            [$user['id']],
            "i"
        );
        
        // Start session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
    }
}

/**
 * Handle logout
 */
function handleLogout() {
    session_start();
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
}

/**
 * Get current logged-in user
 */
function getCurrentUser() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }
    
    try {
        $user = getRow(
            "SELECT id, name, email, phone, role, created_at, last_login FROM users WHERE id = ?",
            [$_SESSION['user_id']],
            "i"
        );
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'user' => $user
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
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
    return $_POST;
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    session_start();
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    session_start();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    session_start();
    return $_SESSION['user_role'] ?? null;
}
?>
