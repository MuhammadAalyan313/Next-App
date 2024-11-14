<?php 

include('db.php');

// Set content type to JSON
header('Content-Type: application/json');

// Function to handle error responses
function handleError($message) {
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from POST request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        handleError('All fields are required..!');
    }

    // Sanitize email to prevent SQL injection
    $email = mysqli_real_escape_string($con, $email);

    // Query to check if the email exists in the database
    $checkEmailExist = "SELECT * FROM users WHERE email='$email'";

    // Check for database query errors
    if (!$result = mysqli_query($con, $checkEmailExist)) {
        handleError('Database error: ' . mysqli_error($con));
    }

    // Verify email and password if user exists
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);    

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Generate a new token
            $token = bin2hex(random_bytes(16));
            
            // Sanitize token and prepare query for updating
            $token = mysqli_real_escape_string($con, $token);
            $insertToken = "UPDATE users SET token='$token' WHERE id={$user['id']}";

            // Check for database query errors
            if (!$fired = mysqli_query($con, $insertToken)) {
                handleError('Failed to update token in database: ' . mysqli_error($con));
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'You logged in successfully..!',
                'token' => $token,
                'data' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            handleError('Invalid password.');
        }
    } else {
        handleError('Email not found.');
    }
} else {
    handleError('Invalid request method.');
}
?>
