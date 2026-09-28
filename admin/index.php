<?php 
require_once '../config.php';
if(!isAdmin()) redirect('../login.php');

// Handle Delete Action
if(isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM assets WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $success = "Asset deleted successfully!";
}

// Fetch All Assets
$stmt = $pdo->query("
    SELECT a.*, c.name as category_name 
    FROM assets a 
    LEFT JOIN categories c ON a.category_id = c.id 
    ORDER BY a.created_at DESC
");
$assets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assets - Tefzon Admin</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">Tefzon Admin</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active"><a href="assets.php">Assets</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="add_asset.php">Add Asset</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Assets</h1>
            <a href="add_asset.php" class="btn btn-primary">+ Add New Asset</a>
        </div>

        <?php if(isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Value</th>
                        <th>Condition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assets as $asset): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($asset['asset_code']) ?></td>
                        <td><?= htmlspecialchars($asset['name']) ?></td>
                        <td><?= htmlspecialchars($asset['category_name']) ?></td>
                        <td>₦<?= number_format($asset['current_value'], 2) ?></td>
                        <td>
                            <span class="badge badge-<?= $asset['condition_status'] == 'Excellent' ? 'success' : 'warning' ?>">
                                <?= $asset['condition_status'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="assets.php?id=<?= $asset['id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $asset['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>