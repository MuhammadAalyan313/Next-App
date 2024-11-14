<?php
// Include your database connection file
include('db.php');
header('Content-Type: application/json');

// Retrieve token from the Authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? $headers['Authorization'] : ''; // Get token from the Authorization header

// Check if token is empty
if (empty($token)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Token is missing. Please log in again.'
    ]);
    exit;
}

// Retrieve the form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $expiration_date = $_POST['expiration_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $plan_name = $_POST['plan_name'] ?? '';
    $user_id = $_POST['user_id'] ?? 0; // The user ID should be provided for checking

    // Validate if all required fields are provided
    if (empty($email) || empty($password) || empty($card_number) || empty($expiration_date) || empty($cvv) || empty($plan_name) || empty($user_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required!'
        ]);
        exit;
    }

    // Validate token existence in the database
    $checkTokenQuery = "SELECT * FROM users WHERE id = '$user_id' AND token = '$token'";
    $result = mysqli_query($con, $checkTokenQuery);

    if (mysqli_num_rows($result) == 0) {
        // Token does not exist or is invalid
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid token. Please log in again.'
        ]);
        exit;
    }

    // If token is valid, proceed with subscription
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Get the current timestamp for the start date
    $start_date = date('Y-m-d H:i:s');

    // Calculate the expiration date (1 month later)
    $end_date = date('Y-m-d H:i:s', strtotime('+1 month'));

    // SQL Query to insert subscription data into the user_subscriptions table
    $query = "INSERT INTO user_subscriptions (user_id, plan_name, start_date, end_date, status, email, password, card_number, expiration_date, cvv) 
              VALUES ('$user_id', '$plan_name', '$start_date', '$end_date', 'active', '$email', '$password_hashed', '$card_number', '$expiration_date', '$cvv')";

    // Execute the query
    if (mysqli_query($con, $query)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Subscription added successfully!',
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add subscription: ' . mysqli_error($con),
        ]);
    }
}
?>
