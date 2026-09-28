<?php 
require_once '../config.php';
if(!isAdmin()) redirect('../login.php');

$stats = [
    'total_assets' => $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn(),
    'total_categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'total_value' => $pdo->query("SELECT SUM(current_value) FROM assets")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tefzon Limited</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">Tefzon Admin</div>
        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php">Dashboard</a></li>
            <li><a href="assets.php">Assets</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="add_asset.php">Add Asset</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <a href="add_asset.php" class="btn btn-primary">+ Add New Asset</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total_assets'] ?></div>
                <div class="stat-label">Total Assets</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total_categories'] ?></div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">₦<?= number_format($stats['total_value'], 2) ?></div>
                <div class="stat-label">Total Asset Value</div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Recent Assets</h2>
                <a href="assets.php" class="btn btn-primary">View All</a>
            </div>
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
                    <?php
                    $stmt = $pdo->query("
                        SELECT a.*, c.name as category_name 
                        FROM assets a 
                        LEFT JOIN categories c ON a.category_id = c.id 
                        ORDER BY a.created_at DESC 
                        LIMIT 10
                    ");
                    while($asset = $stmt->fetch()):
                    ?>
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
                            <a href="edit_asset.php?id=<?= $asset['id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>