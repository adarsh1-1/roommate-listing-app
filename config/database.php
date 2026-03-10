<?php
// Database configuration - AWS RDS Integration
require_once __DIR__ . '/aws-config.php';

// Global database connection
$conn = null;

/**
 * Initialize database connection to AWS RDS
 */
function initializeDbConnection() {
    global $conn;
    
    try {
        $conn = new mysqli(
            AWS_RDS_HOST,
            AWS_RDS_USER,
            AWS_RDS_PASSWORD,
            AWS_RDS_DATABASE,
            AWS_RDS_PORT
        );
        
        if ($conn->connect_error) {
            throw new Exception("RDS Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset(AWS_RDS_CHARSET);
        return true;
    } catch(Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Get database connection instance
 */
function getDbConnection() {
    global $conn;
    if (!$conn) {
        initializeDbConnection();
    }
    return $conn;
}

/**
 * Close database connection
 */
function closeDbConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
        $conn = null;
    }
}

/**
 * Safely escape strings for database queries
 * @deprecated Use prepared statements instead
 */
function safe_input($data) {
    $conn = getDbConnection();
    return $conn->real_escape_string(strip_tags(trim($data)));
}

/**
 * Execute a prepared statement
 * @param string $query - SQL query with placeholders (?)
 * @param array $params - Parameters to bind
 * @param string $types - Type string ('s' for string, 'i' for int, 'd' for double)
 * @return mysqli_result|bool
 */
function executeQuery($query, $params = [], $types = '') {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params) && !empty($types)) {
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("Bind failed: " . $stmt->error);
        }
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    return $stmt;
}

/**
 * Execute query and get single row
 */
function getRow($query, $params = [], $types = '') {
    $stmt = executeQuery($query, $params, $types);
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row;
}

/**
 * Execute query and get all rows
 */
function getAllRows($query, $params = [], $types = '') {
    $stmt = executeQuery($query, $params, $types);
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

/**
 * Execute INSERT query and return inserted ID
 */
function insertRecord($query, $params = [], $types = '') {
    $conn = getDbConnection();
    $stmt = executeQuery($query, $params, $types);
    $insertId = $conn->insert_id;
    $stmt->close();
    return $insertId;
}

/**
 * Execute UPDATE or DELETE query and return affected rows
 */
function updateRecord($query, $params = [], $types = '') {
    $conn = getDbConnection();
    $stmt = executeQuery($query, $params, $types);
    $affectedRows = $conn->affected_rows;
    $stmt->close();
    return $affectedRows;
}

// Auto-initialize connection on include
try {
    initializeDbConnection();
} catch (Exception $e) {
    error_log("Failed to initialize database: " . $e->getMessage());
}
?>

