<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eve_furniture');
define('DB_CHARSET', 'utf8mb4');

// Error reporting (development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection with error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    if (!$conn->set_charset(DB_CHARSET)) {
        throw new Exception("Error setting charset: " . $conn->error);
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("We're experiencing technical difficulties. Please try again later.");
}

/**
 * Secure database query helper
 */
function db_query($sql, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
            
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    return $stmt;
}

function db_fetch_one($sql, $params = []) {
    $stmt = db_query($sql, $params);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function db_fetch_value($sql, $params = []) {
    $row = db_fetch_one($sql, $params);
    return $row ? reset($row) : null;
}