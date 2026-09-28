<?php 
require_once '../config.php';
if(!isAdmin()) redirect('../login.php');

// Handle category operations
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        if($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$_POST['name'], $_POST['description']]);
            $success = "Category added successfully!";
        } elseif($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Category deleted successfully!";
        }
    }
}

$categories = $pdo->query("
    SELECT c.*, COUNT(a.id) as asset_count 
    FROM categories c 
    LEFT JOIN assets a ON c.id = a.category_id 
    GROUP BY c.id 
    ORDER BY c.name
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Tefzon Admin</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">Tefzon Admin</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="assets.php">Assets</a></li>
            <li class="active"><a href="categories.php">Categories</a></li>
            <li><a href="add_asset.php">Add Asset</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Category Management</h1>
        </div>

        <?php if(isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <div class="section">
            <h2 style="margin-bottom: 1.5rem;">Add New Category</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>

        <div class="section">
            <h2>Existing Categories</h2>
            <div class="category-grid">
                <?php foreach($categories as $cat): ?>
                    <div class="category-card">
                        <h3><?= htmlspecialchars($cat['name']) ?></h3>
                        <p><?= htmlspecialchars($cat['description'] ?? 'No description') ?></p>
                        <div class="category-stats">
                            <span style="color: #667eea; font-weight: bold;">
                                <?= $cat['asset_count'] ?> Assets
                            </span>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Delete this category? Assets will not be deleted.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>