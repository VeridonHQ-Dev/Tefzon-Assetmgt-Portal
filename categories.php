<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Tefzon Limited</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem 5%; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; }
        .nav-links a { color: white; text-decoration: none; margin-left: 2rem; }
        .container { max-width: 1200px; margin: 3rem auto; padding: 0 2rem; }
        .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem; }
        .category-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s; cursor: pointer; }
        .category-card:hover { transform: translateY(-5px); }
        .category-card h2 { color: #333; margin-bottom: 1rem; }
        .category-card p { color: #666; margin-bottom: 1.5rem; line-height: 1.5; }
        .asset-count { color: #667eea; font-weight: bold; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo">Tefzon Limited</div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="categories.php">Categories</a>
                <?php if(isAdmin()): ?>
                    <a href="admin/dashboard.php">Dashboard</a>
                <?php endif; ?>
                <?php if(isLoggedIn()): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Asset Categories</h1>
        <div class="category-grid">
            <?php
            $categories = $pdo->query("
                SELECT c.*, COUNT(a.id) as asset_count 
                FROM categories c 
                LEFT JOIN assets a ON c.id = a.category_id 
                GROUP BY c.id 
                ORDER BY c.name
            ")->fetchAll();
            
            foreach($categories as $cat):
            ?>
            <div class="category-card" onclick="window.location='index.php?category=<?= $cat['id'] ?>'">
                <h2><?= htmlspecialchars($cat['name']) ?></h2>
                <p><?= htmlspecialchars($cat['description'] ?? 'No description available.') ?></p>
                <div class="asset-count"><?= $cat['asset_count'] ?> Assets</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>