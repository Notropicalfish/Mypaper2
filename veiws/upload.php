<?php
// MyPaper - Upload View v2.0 by Peelish Studios
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['wallpaper'])) {
    $title = trim($_POST['title']);
    $tags = trim($_POST['tags']);
    $user_id = $_SESSION['user_id'];
    
    // --- File Upload Logic ---
    $target_dir = "uploads/";
    $file_extension = strtolower(pathinfo($_FILES["wallpaper"]["name"], PATHINFO_EXTENSION));
    // Create a unique filename to prevent overwriting
    $new_filename = uniqid('', true) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Validation checks
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        $message = "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
    } elseif ($_FILES["wallpaper"]["size"] > 5000000) { // 5MB limit
        $message = "Error: Your file is too large (Max 5MB).";
    } elseif (empty($title) || empty($tags)) {
        $message = "Error: Please provide a title and at least one tag.";
    } else {
        if (move_uploaded_file($_FILES["wallpaper"]["tmp_name"], $target_file)) {
            // --- Instant Approval Logic ---
            // Check user's badge. If admin or verified, approve instantly.
            $status = 'pending';
            if (isset($_SESSION['user_badge']) && in_array($_SESSION['user_badge'], ['admin', 'verified'])) {
                $status = 'approved';
            }

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO wallpapers (user_id, title, tags, file_path, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $title, $tags, $target_file, $status);
            
            if ($stmt->execute()) {
                $message = "Upload successful! Your wallpaper is now " . ($status === 'pending' ? "pending review." : "live.");
            } else {
                $message = "Error: Could not save wallpaper details to database.";
            }
            $stmt->close();
        } else {
            $message = "Error: There was an error uploading your file.";
        }
    }
}
?>

<div class="form-container">
    <h2>Upload Wallpaper</h2>
    <p>Share your amazing wallpapers with the world.</p>
    
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'Error') === 0 ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?page=upload" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required placeholder="e.g., Mountain Sunset">
        </div>
        <div class="form-group">
            <label for="tags">Tags (comma-separated)</label>
            <input type="text" id="tags" name="tags" required placeholder="e.g., nature, 4k, landscape, orange">
        </div>
        <div class="form-group">
            <label for="wallpaper">Wallpaper File</label>
            <input type="file" id="wallpaper" name="wallpaper" required accept="image/*">
        </div>
        <button type="submit" class="button button-primary">Upload</button>
    </form>
</div>

