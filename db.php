<?php
// --- Database Configuration ---
// Replace these values with your actual database credentials from Hostinger.
$servername = "localhost"; // Usually localhost for Hostinger
$username = "u506144009_mypaper"; // This looks correct from your image
$password = "MyPaper1!"; // IMPORTANT: Replace this with the real password you set in Hostinger
$dbname = "u506144009_Mypaper"; // This looks correct from your image

// --- Establish Connection ---
// Create a new MySQLi object to connect to the database.
$conn = new mysqli($servername, $username, $password, $dbname);

// --- Connection Check ---
// Check if the connection was successful. If not, terminate the script
// and display an error message. This prevents further PHP errors if the
// database is unavailable.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Start Session ---
// session_start() creates a session or resumes the current one. This is
// essential for user authentication (e.g., keeping users logged in).
// We'll also add security headers here.
session_start([
    'cookie_secure' => true, // Only send cookie over HTTPS
    'cookie_httponly' => true, // Prevent client-side script access
    'cookie_samesite' => 'Lax' // CSRF protection
]);


// --- Moderator/Admin & Badge Check ---
// After the session starts, check if the logged-in user is the administrator.
// This makes the admin status and badge available across the entire site.
if (isset($_SESSION['user_id'])) {
    // It's more secure to re-fetch this from the DB than to trust the session alone
    // This prevents a user from staying admin if their status is revoked.
    $stmt = $conn->prepare("SELECT badge FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_badge'] = $user['badge'];
        $_SESSION['is_admin'] = ($user['badge'] === 'admin');
    }
    $stmt->close();
} else {
    // Ensure these are not set if the user is not logged in
    $_SESSION['is_admin'] = false;
    $_SESSION['user_badge'] = 'user';
}
?>

