<?php 

include('db.php');

// Function to handle error responses
function handleError($message) {
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check if all fields are filled
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        handleError('All fields are required..!!');
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        handleError('Password and Confirm password do not match...!');
    }

    // Sanitize the inputs to prevent SQL injection
    $username = mysqli_real_escape_string($con, $username);
    $email = mysqli_real_escape_string($con, $email);

    // Check if the username or email already exists
    $existanceQuery = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $checkEmailExist = mysqli_query($con, $existanceQuery);
    if (mysqli_num_rows($checkEmailExist) > 0) {
        handleError('User already exists...!!');
    }

    // Hash the password for storage
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user into the database
    $insertQuery = "INSERT INTO users (username, email, password, date) VALUES ('$username', '$email', '$hashedPassword', current_timestamp())";

    if (!mysqli_query($con, $insertQuery)) {
        handleError('Database error: ' . mysqli_error($con));
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'You have registered successfully..!',
        'data' => [
            'username' => $username,
            'email' => $email
        ]
    ]);
}
?>
