<?php
// MyPaper - Single Wallpaper View v1.0 by Peelish Studios

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?page=home');
    exit;
}

$wallpaper_id = intval($_GET['id']);

// --- Increment View Count ---
$update_view = $conn->prepare("UPDATE wallpapers SET views = views + 1 WHERE id = ?");
$update_view->bind_param("i", $wallpaper_id);
$update_view->execute();
$update_view->close();

// --- Fetch Wallpaper Details ---
$stmt = $conn->prepare(
    "SELECT w.*, u.username, u.badge 
    FROM wallpapers w 
    JOIN users u ON w.user_id = u.id 
    WHERE w.id = ?"
);
$stmt->bind_param("i", $wallpaper_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='container'>Wallpaper not found.</p>";
} else {
    $wallpaper = $result->fetch_assoc();
    $tags = explode(',', $wallpaper['tags']);
?>

<div class="wallpaper-detail-page">
    <div class="wallpaper-image-container">
        <img src="<?php echo htmlspecialchars($wallpaper['file_path']); ?>" alt="<?php echo htmlspecialchars($wallpaper['title']); ?>">
    </div>
    <div class="wallpaper-info-container">
        <h1><?php echo htmlspecialchars($wallpaper['title']); ?></h1>
        
        <div class="uploader-info">
            Uploaded by <a href="#"><?php echo htmlspecialchars($wallpaper['username']); ?></a>
            <span class="badge badge-<?php echo htmlspecialchars($wallpaper['badge']); ?>"><?php echo htmlspecialchars(ucfirst($wallpaper['badge'])); ?></span>
        </div>

        <div class="wallpaper-stats">
            <span><i class="fas fa-eye"></i> <?php echo number_format($wallpaper['views']); ?> Views</span>
            <span><i class="fas fa-download"></i> <?php echo number_format($wallpaper['downloads']); ?> Downloads</span>
        </div>
        
        <a href="index.php?action=download&id=<?php echo $wallpaper['id']; ?>" class="button button-primary download-button">
            <i class="fas fa-download"></i> Download
        </a>
        
        <div class="tags-container">
            <h3>Tags</h3>
            <div class="tags">
                <?php foreach($tags as $tag): ?>
                    <a href="index.php?page=home&tag=<?php echo urlencode(trim($tag)); ?>" class="tag"><?php echo htmlspecialchars(trim($tag)); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php } $stmt->close(); ?>

