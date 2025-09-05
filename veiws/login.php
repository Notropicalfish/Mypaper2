<?php
if (isset($_SESSION['user_id'])) { header("Location: index.php?page=profile"); exit(); }
$errors = []; $email = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action']) && $_GET['action'] == 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($email) || empty($password)) { $errors[] = "All fields are required."; }
    else {
        $stmt = $conn->prepare("SELECT id, username, password, badge FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_badge'] = $user['badge'];
                $_SESSION['is_admin'] = ($user['badge'] === 'admin');
                header("Location: index.php?page=profile");
                exit();
            } else { $errors[] = "Invalid email or password."; }
        } else { $errors[] = "Invalid email or password."; }
        $stmt->close();
    }
}
?>
<div class="container auth-form">
    <?php if (!empty($errors)): ?><div class="error-messages"><?php foreach ($errors as $error) echo "<p>$error</p>"; ?></div><?php endif; ?>
    <?php if (isset($_GET['registered'])): ?><div class="success-message"><p>Registration successful! Please log in.</p></div><?php endif; ?>
    <form action="index.php?page=login&action=login" method="post">
        <div class="form-group"><label for="email">Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required></div>
        <div class="form-group"><label for="password">Password</label><input type="password" name="password" required></div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p class="auth-link">Don't have an account? <a href="index.php?page=register">Sign Up</a></p>
</div>
