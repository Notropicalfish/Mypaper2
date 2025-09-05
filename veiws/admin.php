<?php
// MyPaper - Admin Panel View v1.0 by Peelish Studios

// Security Check: Ensure the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: index.php?page=home');
    exit;
}

// --- Fetch Pending Wallpapers ---
$pending_stmt = $conn->prepare("SELECT w.*, u.username FROM wallpapers w JOIN users u ON w.user_id = u.id WHERE w.status = 'pending' ORDER BY w.id DESC");
$pending_stmt->execute();
$pending_wallpapers = $pending_stmt->get_result();

// --- Fetch All Users ---
$users_stmt = $conn->prepare("SELECT id, username, email, badge FROM users ORDER BY id ASC");
$users_stmt->execute();
$all_users = $users_stmt->get_result();

?>

<div class="admin-panel">
    <h1>Admin Panel</h1>
    
    <!-- Pending Approvals Section -->
    <div class="admin-section">
        <h2>Pending Approvals</h2>
        <?php if ($pending_wallpapers->num_rows > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Title</th>
                        <th>Uploader</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $pending_wallpapers->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="preview" class="table-preview"></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['tags']); ?></td>
                        <td>
                            <a href="index.php?action=approve_wallpaper&id=<?php echo $row['id']; ?>" class="button button-success">Approve</a>
                            <a href="index.php?action=decline_wallpaper&id=<?php echo $row['id']; ?>" class="button button-danger">Decline</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No wallpapers are currently pending approval. Great job!</p>
        <?php endif; ?>
    </div>

    <!-- User Management Section -->
    <div class="admin-section">
        <h2>User Management</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Badge</th>
                    <th>Set Badge</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $all_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge badge-<?php echo $user['badge']; ?>"><?php echo ucfirst($user['badge']); ?></span></td>
                    <td>
                        <?php if ($user['id'] !== $_SESSION['user_id']): // Admin can't change their own badge ?>
                        <form action="index.php?action=manage_user" method="POST" class="badge-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="new_badge">
                                <option value="user" <?php if($user['badge'] == 'user') echo 'selected'; ?>>User</option>
                                <option value="verified" <?php if($user['badge'] == 'verified') echo 'selected'; ?>>Verified</option>
                                <option value="professional" <?php if($user['badge'] == 'professional') echo 'selected'; ?>>Professional</option>
                                <option value="admin" <?php if($user['badge'] == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                            <button type="submit" class="button">Set</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

