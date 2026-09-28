<?php require_once '../config.php';

if(!isset($_GET['id'])) {
    redirect('index.php');
}

$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name 
    FROM assets a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.id = ?
");
$stmt->execute([$_GET['id']]);
$asset = $stmt->fetch();

if(!$asset) {
    redirect('index.php');
}

$images_stmt = $pdo->prepare("SELECT * FROM asset_images WHERE asset_id = ? ORDER BY is_primary DESC");
$images_stmt->execute([$asset['id']]);
$images = $images_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($asset['name']) ?> - Tefzon Limited</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }
        
        .asset-detail {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .asset-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            padding: 3rem;
        }
        
        .image-gallery {
            position: relative;
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .thumbnail-row {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
        }
        
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s;
        }
        
        .thumbnail:hover,
        .thumbnail.active {
            opacity: 1;
        }
        
        .asset-info h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .asset-code {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .asset-price {
            font-size: 2rem;
            color: #667eea;
            font-weight: bold;
            margin: 1.5rem 0;
        }
        
        .info-section {
            margin: 2rem 0;
        }
        
        .info-section h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .barcode-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            margin-top: 2rem;
        }
        
        .barcode-section img {
            max-width: 100%;
            height: auto;
            margin-top: 1rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .badge-excellent {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .badge-good {
            background: #bee3f8;
            color: #2c5282;
        }
        
        .badge-fair {
            background: #feebc8;
            color: #744210;
        }
        
        .badge-poor {
            background: #fed7d7;
            color: #742a2a;
        }

        @media (max-width: 768px) {
            .asset-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo">Tefzon Limited</div>
            <div class="nav-links">
                <a href="../index.php">Home</a>
                <a href="../categories.php">Categories</a>
                <?php if(isAdmin()): ?>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="asset-detail">
            <div class="asset-layout">
                <div class="image-gallery">
                    <img src="../<?= htmlspecialchars($images[0]['image_path'] ?? 'uploads/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($asset['name']) ?>" 
                         class="main-image" 
                         id="mainImage">
                    
                    <div class="thumbnail-row">
                        <?php foreach($images as $index => $img): ?>
                            <img src="../<?= htmlspecialchars($img['image_path']) ?>" 
                                 alt="Thumbnail" 
                                 class="thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                 onclick="changeImage('../<?= htmlspecialchars($img['image_path']) ?>', this)">
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="asset-info">
                    <div class="asset-code">#<?= htmlspecialchars($asset['asset_code']) ?></div>
                    <h1><?= htmlspecialchars($asset['name']) ?></h1>
                    <p style="color: #666; margin: 0.5rem 0;">Category: <?= htmlspecialchars($asset['category_name']) ?></p>
                    
                    <div class="asset-price">₦<?= number_format($asset['current_value'], 2) ?></div>
                    
                    <div style="margin: 1.5rem 0;">
                        <span class="badge badge-<?= strtolower($asset['condition_status']) ?>">
                            <?= $asset['condition_status'] ?> Condition
                        </span>
                    </div>

                    <div class="info-section">
                        <h3>Description</h3>
                        <p style="line-height: 1.6; color: #555;">
                            <?= nl2br(htmlspecialchars($asset['description'])) ?>
                        </p>
                    </div>

                    <?php if($asset['specifications']): ?>
                    <div class="info-section">
                        <h3>Specifications</h3>
                        <p style="line-height: 1.6; color: #555;">
                            <?= nl2br(htmlspecialchars($asset['specifications'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="info-section">
                        <h3>Asset Details</h3>
                        <?php if($asset['purchase_date']): ?>
                        <div class="info-row">
                            <span class="info-label">Purchase Date:</span>
                            <span class="info-value"><?= date('F d, Y', strtotime($asset['purchase_date'])) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($asset['purchase_price']): ?>
                        <div class="info-row">
                            <span class="info-label">Purchase Price:</span>
                            <span class="info-value">₦<?= number_format($asset['purchase_price'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($asset['location']): ?>
                        <div class="info-row">
                            <span class="info-label">Location:</span>
                            <span class="info-value"><?= htmlspecialchars($asset['location']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-row">
                            <span class="info-label">Added:</span>
                            <span class="info-value"><?= date('F d, Y', strtotime($asset['created_at'])) ?></span>
                        </div>
                    </div>

                    <?php if($asset['barcode_path']): ?>
                    <div class="barcode-section">
                        <h4>Asset Barcode</h4>
                        <img src="../<?= htmlspecialchars($asset['barcode_path']) ?>" alt="Barcode">
                        <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #666;">
                            Scan to view asset details
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeImage(src, thumb) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }
    </script>
</body>
</html>