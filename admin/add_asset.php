<?php 
require_once '../config.php';
if(!isAdmin()) redirect('../login.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_code = 'AST' . time();
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $specifications = $_POST['specifications'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = $_POST['purchase_price'];
    $current_value = $_POST['current_value'];
    $condition_status = $_POST['condition_status'];
    $location = $_POST['location'];
    
    // Generate barcode
    require_once 'barcode.php';
    $barcode_path = generateBarcode($asset_code);
    
    $stmt = $pdo->prepare("INSERT INTO assets (asset_code, name, category_id, description, specifications, 
                          purchase_date, purchase_price, current_value, condition_status, location, barcode_path) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$asset_code, $name, $category_id, $description, $specifications, 
                    $purchase_date, $purchase_price, $current_value, $condition_status, $location, $barcode_path]);
    
    $asset_id = $pdo->lastInsertId();
    
    // Handle multiple image uploads
    if(isset($_FILES['images'])) {
        $upload_dir = '../uploads/assets/';
        if(!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if($_FILES['images']['error'][$key] === 0) {
                $filename = uniqid() . '_' . $_FILES['images']['name'][$key];
                $filepath = $upload_dir . $filename;
                
                if(move_uploaded_file($tmp_name, $filepath)) {
                    $is_primary = ($key === 0) ? 1 : 0;
                    $stmt = $pdo->prepare("INSERT INTO asset_images (asset_id, image_path, is_primary) VALUES (?, ?, ?)");
                    $stmt->execute([$asset_id, 'uploads/assets/' . $filename, $is_primary]);
                }
            }
        }
    }
    
    redirect('assets.php?success=1');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Asset - Tefzon Limited</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">Tefzon Admin</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="assets.php">Assets</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li class="active"><a href="add_asset.php">Add Asset</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Add New Asset</h1>
        </div>

        <div class="section">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Asset Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>Specifications</label>
                    <textarea name="specifications" placeholder="e.g., Brand: HP, Model: EliteBook 840, RAM: 16GB"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Purchase Date</label>
                        <input type="date" name="purchase_date">
                    </div>
                    
                    <div class="form-group">
                        <label>Purchase Price (₦)</label>
                        <input type="number" step="0.01" name="purchase_price">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Current Value (₦) *</label>
                        <input type="number" step="0.01" name="current_value" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Condition *</label>
                        <select name="condition_status" required>
                            <option value="Excellent">Excellent</option>
                            <option value="Good" selected>Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g., Office Floor 2, Room 205">
                </div>

                <div class="form-group">
                    <label>Upload Images (Multiple) *</label>
                    <input type="file" name="images[]" multiple accept="image/*" required>
                    <small>First image will be set as primary</small>
                </div>

                <button type="submit" class="btn btn-success">Add Asset</button>
                <a href="assets.php" class="btn" style="background: #e0e0e0; color: #333; margin-left: 1rem;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>