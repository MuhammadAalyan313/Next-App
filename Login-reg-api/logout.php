<?php
// Database connection
include('db.php');

// Function to handle error responses
function handleError($message) {
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

// Get token from the Authorization header
$headers = getallheaders();
$token = $headers['Authorization'] ?? '';

// If no token is provided
if (empty($token)) {
    handleError('No token provided');
}

// Remove "Bearer " prefix if it exists (optional)
if (strpos($token, 'Bearer ') === 0) {
    $token = substr($token, 7); // Remove 'Bearer ' prefix
}

// Log the token for debugging
error_log("Token received: " . $token); // Logs the token in the error log file

// Sanitize token to prevent SQL injection
$token = mysqli_real_escape_string($con, $token);

// Query to check if the token exists and exactly matches the one in the database
$query = "SELECT id FROM users WHERE token = '$token' LIMIT 1";
$result = mysqli_query($con, $query);

// Check if a matching token exists in the database
if (mysqli_num_rows($result) > 0) {
    // Token exists and matches
    $row = mysqli_fetch_assoc($result);
    $userId = $row['id'];
    
    // Log successful token match for debugging
    error_log("Token found for user ID: " . $userId);

    // Clear the token from the database (log the user out)
    $clearTokenQuery = "UPDATE users SET token = NULL WHERE id = '$userId'";

    if (mysqli_query($con, $clearTokenQuery)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    } else {
        handleError('Failed to log out');
    }
} else {
    // Token doesn't exist or doesn't match
    handleError('Invalid token or not logged in');
}

mysqli_close($con);
?>
