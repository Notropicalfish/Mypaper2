<?php
// MyPaper - Home View v3.0 by Peelish Studios (Shows all uploads)

// Get filters from URL
$current_tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$popular_tags = ['popular', 'abstract', 'architecture', 'beauty', 'art', 'nature', 'animal', 'cartoon', '4k', '8k'];
?>

<div class="main-content">
    <div class="page-header">
        <h1>Wallpapers</h1>
        <form action="index.php" method="GET" class="search-bar">
            <input type="hidden" name="page" value="home">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search People, Moods, Fashion..." value="<?php echo htmlspecialchars($search_query); ?>">
        </form>
    </div>

    <nav class="tag-navigation">
        <ul>
            <?php foreach ($popular_tags as $tag): ?>
                <li>
                    <a href="index.php?page=home&tag=<?php echo urlencode($tag); ?>" class="<?php echo ($current_tag === $tag) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars(ucfirst($tag)); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="wallpaper-grid">
        <?php
        // --- FIX: This query now fetches ALL wallpapers, regardless of status ---
        $sql = "SELECT w.*, u.username, u.badge FROM wallpapers w JOIN users u ON w.user_id = u.id";
        
        $params = [];
        $types = '';

        $conditions = [];
        if (!empty($current_tag)) {
            $conditions[] = "FIND_IN_SET(?, w.tags)";
            $types .= 's';
            $params[] = $current_tag;
        }
        if (!empty($search_query)) {
            $conditions[] = "(w.title LIKE ? OR w.tags LIKE ?)";
            $types .= 'ss';
            $search_param = "%{$search_query}%";
            $params[] = $search_param;
            $params[] = $search_param;
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY w.id DESC";
        
        $stmt = $conn->prepare($sql);

        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            while($row = $result->fetch_assoc()):
        ?>
            <a href="index.php?page=wallpaper&id=<?php echo $row['id']; ?>" class="wallpaper-card">
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy">
                </div>
                <div class="info">
                    <h3 class="title"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="uploader">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($row['username']); ?></span>
                        <?php
                            // Display badge icon based on user's badge
                            $badge_class = 'fas fa-user'; // Default
                            if ($row['badge'] === 'admin') $badge_class = 'fas fa-shield-alt text-admin';
                            if ($row['badge'] === 'verified') $badge_class = 'fas fa-check-circle text-verified';
                            if ($row['badge'] === 'professional') $badge_class = 'fas fa-briefcase text-professional';
                         ?>
                        <i class="<?php echo $badge_class; ?>" title="<?php echo ucfirst($row['badge']); ?>"></i>
                    </div>
                </div>
            </a>
        <?php
            endwhile;
        else:
        ?>
            <p class="no-results">No wallpapers found. Why not be the first to upload one?</p>
        <?php endif; 
        $stmt->close();
        ?>
    </div>
</div>

