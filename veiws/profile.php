<?php
// MyPaper - Profile View v1.0 by Peelish Studios

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    // This should not happen if the router is working, but it's a good safeguard
    header('Location: index.php?page=login');
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Fetch User Info and Creator Stats ---
$stmt = $conn->prepare(
    "SELECT u.username, u.badge, 
    COUNT(w.id) as upload_count, 
    COALESCE(SUM(w.views), 0) as total_views, 
    COALESCE(SUM(w.downloads), 0) as total_downloads
    FROM users u
    LEFT JOIN wallpapers w ON u.id = w.user_id
    WHERE u.id = ?
    GROUP BY u.id"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// --- Fetch User's Uploaded Wallpapers ---
$stmt = $conn->prepare("SELECT * FROM wallpapers WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wallpapers_result = $stmt->get_result();

?>

<div class="profile-page">
    <div class="profile-header">
        <i class="fas fa-user-circle profile-avatar"></i>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user_stats['username']); ?></h1>
            <span class="badge badge-<?php echo htmlspecialchars($user_stats['badge']); ?>"><?php echo htmlspecialchars(ucfirst($user_stats['badge'])); ?></span>
        </div>
    </div>

    <div class="profile-tabs">
        <div class="tab-content active" id="diagnostics">
            <h2>Creator Diagnostics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-upload"></i>
                    <h3><?php echo $user_stats['upload_count']; ?></h3>
                    <p>Uploads</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-eye"></i>
                    <h3><?php echo number_format($user_stats['total_views']); ?></h3>
                    <p>Total Views</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-download"></i>
                    <h3><?php echo number_format($user_stats['total_downloads']); ?></h3>
                    <p>Total Downloads</p>
                </div>
            </div>
        </div>

        <div class="tab-content" id="uploads">
             <h2>My Uploads</h2>
            <?php if ($wallpapers_result->num_rows > 0): ?>
                <div class="wallpaper-grid">
                    <?php while($row = $wallpapers_result->fetch_assoc()): ?>
                         <a href="index.php?page=wallpaper&id=<?php echo $row['id']; ?>" class="wallpaper-card">
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy">
                                <span class="status-pill status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-results">You haven't uploaded any wallpapers yet!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

