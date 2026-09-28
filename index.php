<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tefzon Limited - Asset Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 5%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 2rem;
            transition: opacity 0.3s;
        }
        
        .nav-links a:hover {
            opacity: 0.8;
        }
        
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 5%;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .container {
            max-width: 1400px;
            margin: 3rem auto;
            padding: 0 5%;
        }
        
        .search-bar {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .search-bar input {
            width: 70%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .search-bar button {
            padding: 1rem 2rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 1rem;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .filters select {
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            background: white;
        }
        
        .asset-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .asset-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .asset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .asset-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .asset-info {
            padding: 1.5rem;
        }
        
        .asset-code {
            color: #667eea;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .asset-name {
            font-size: 1.3rem;
            margin: 0.5rem 0;
            color: #333;
        }
        
        .asset-category {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .asset-price {
            font-size: 1.1rem;
            color: #667eea;
            font-weight: bold;
            margin-top: 1rem;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }
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

    <div class="hero">
        <h1>Asset Management System</h1>
        <p>Track, manage, and monitor all your company assets in one place</p>
    </div>

    <div class="container">
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search assets by name or code..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="filters">
            <select name="category" onchange="filterByCategory(this.value)">
                <option value="">All Categories</option>
                <?php
                $cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
                foreach($cats as $cat):
                ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="asset-grid">
            <?php
            $where = "1=1";
            $params = [];
            
            if(isset($_GET['search']) && !empty($_GET['search'])) {
                $where .= " AND (a.name LIKE ? OR a.asset_code LIKE ?)";
                $search = '%' . $_GET['search'] . '%';
                $params[] = $search;
                $params[] = $search;
            }
            
            if(isset($_GET['category']) && !empty($_GET['category'])) {
                $where .= " AND a.category_id = ?";
                $params[] = $_GET['category'];
            }
            
            $stmt = $pdo->prepare("
                SELECT a.*, c.name as category_name, 
                       (SELECT image_path FROM asset_images WHERE asset_id = a.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM assets a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE $where
                ORDER BY a.created_at DESC
            ");
            $stmt->execute($params);
            $assets = $stmt->fetchAll();
            
            if(count($assets) > 0):
                foreach($assets as $asset):
                    $image = $asset['primary_image'] ?? 'uploads/placeholder.jpg';
            ?>
                <div class="asset-card" onclick="window.location='asset.php?id=<?= $asset['id'] ?>'">
                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($asset['name']) ?>" class="asset-image">
                    <div class="asset-info">
                        <div class="asset-code">#<?= htmlspecialchars($asset['asset_code']) ?></div>
                        <h3 class="asset-name"><?= htmlspecialchars($asset['name']) ?></h3>
                        <div class="asset-category"><?= htmlspecialchars($asset['category_name']) ?></div>
                        <div class="asset-price">₦<?= number_format($asset['current_value'], 2) ?></div>
                    </div>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <p>No assets found.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Tefzon Limited. All rights reserved.</p>
    </footer>

    <script>
        function filterByCategory(catId) {
            const url = new URL(window.location.href);
            if(catId) {
                url.searchParams.set('category', catId);
            } else {
                url.searchParams.delete('category');
            }
            window.location.href = url.toString();
        }
    </script>
</body>
</html>