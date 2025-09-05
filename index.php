<?php
// MyPaper - Main Controller/Router v4.0 by Peelish Studios
require_once 'db.php';

// --- Action Handler ---
$action = $_GET['action'] ?? '';
$admin_only_actions = ['approve_wallpaper', 'decline_wallpaper', 'manage_user'];

// Security check for admin actions
if (in_array($action, $admin_only_actions) && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true)) {
    header("Location: index.php?page=home&error=Access Denied");
    exit;
}

switch ($action) {
    case 'logout':
        session_unset(); session_destroy(); header("Location: index.php"); exit;

    case 'download':
        // This download logic is already functional.
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: index.php"); exit; }
        $wallpaper_id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT file_path FROM wallpapers WHERE id = ? AND status = 'approved'");
        $stmt->bind_param("i", $wallpaper_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $wallpaper = $result->fetch_assoc();
            $filepath = $wallpaper['file_path'];
            if (file_exists($filepath)) {
                $update_stmt = $conn->prepare("UPDATE wallpapers SET downloads = downloads + 1 WHERE id = ?");
                $update_stmt->bind_param("i", $wallpaper_id);
                $update_stmt->execute();
                $update_stmt->close();
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));
                flush();
                readfile($filepath);
                exit;
            }
        }
        header("Location: index.php?page=home&error=File not found.");
        exit;
    
    case 'approve_wallpaper':
        $stmt = $conn->prepare("UPDATE wallpapers SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        header("Location: index.php?page=admin&message=Wallpaper approved!");
        exit;

    case 'decline_wallpaper':
        $stmt = $conn->prepare("SELECT file_path FROM wallpapers WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $file = $res->fetch_assoc();
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }
        }
        $stmt = $conn->prepare("DELETE FROM wallpapers WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        header("Location: index.php?page=admin&message=Wallpaper declined and removed.");
        exit;

    case 'manage_user':
        $user_id = $_POST['user_id'];
        $new_badge = $_POST['new_badge'];
        $stmt = $conn->prepare("UPDATE users SET badge = ? WHERE id = ?");
        $stmt->bind_param("si", $new_badge, $user_id);
        $stmt->execute();
        header("Location: index.php?page=admin&message=User badge updated.");
        exit;
}

// --- Page Router ---
$page = $_GET['page'] ?? 'home';
$pageTitle = ucfirst($page);
require_once 'partials/header.php';

switch ($page) {
    case 'home': require_once 'views/home.php'; break;
    case 'login': require_once 'views/login.php'; break;
    case 'register': require_once 'views/register.php'; break;
    case 'upload':
        if (!isset($_SESSION['user_id'])) { header("Location: index.php?page=login&error=Please log in to upload."); exit; }
        require_once 'views/upload.php'; break;
    case 'profile':
        if (!isset($_SESSION['user_id'])) { header("Location: index.php?page=login&error=Please log in to view your profile."); exit; }
        require_once 'views/profile.php'; break;
    case 'admin':
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) { header("Location: index.php?page=home&error=Access denied."); exit; }
        require_once 'views/admin.php'; break;
    case 'wallpaper':
        if (!isset($_GET['id'])) { header("Location: index.php?page=home&error=Wallpaper not found."); exit; }
        require_once 'views/wallpaper.php'; break;
    default:
        http_response_code(404);
        echo "<p class='container'>Error 404: Page not found.</p>";
        break;
}

require_once 'partials/footer.php';
?>

