<?php
if (isset($_SESSION['user_id'])) { header("Location: index.php?page=profile"); exit(); }
$errors = []; $username = $email = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action']) && $_GET['action'] == 'register') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($username) || empty($email) || empty($password)) { $errors[] = "All fields are required."; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Invalid email format."; }
    elseif (strlen($password) < 6) { $errors[] = "Password must be at least 6 characters."; }
    else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($insert_stmt->execute()) {
                header("Location: index.php?page=login&registered=true");
                exit();
            } else { $errors[] = "Registration failed. Please try again."; }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>
<div class="container auth-form">
     <?php if (!empty($errors)): ?><div class="error-messages"><?php foreach ($errors as $error) echo "<p>$error</p>"; ?></div><?php endif; ?>
    <form action="index.php?page=register&action=register" method="post">
        <div class="form-group"><label for="username">Username</label><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required></div>
        <div class="form-group"><label for="email">Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required></div>
        <div class="form-group"><label for="password">Password</label><input type="password" name="password" required></div>
        <button type="submit" class="btn btn-primary">Create Account</button>
    </form>
    <p class="auth-link">Already have an account? <a href="index.php?page=login">Login</a></p>
</div>
