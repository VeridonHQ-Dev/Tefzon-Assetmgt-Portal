<?php
/**
 * Barcode Generator for Tefzon Asset Management System
 * Generates barcode images using free online API
 */

function generateBarcode($code) {
    // Using free barcode API
    $barcode_url = "https://barcode.tec-it.com/barcode.ashx?data=" . urlencode($code) . 
                   "&code=Code128&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96" .
                   "&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0";
    
    $barcode_dir = '../uploads/barcodes/';
    
    // Create directory if it doesn't exist
    if(!file_exists($barcode_dir)) {
        mkdir($barcode_dir, 0777, true);
    }
    
    $filename = $code . '.gif';
    $filepath = $barcode_dir . $filename;
    
    // Download and save barcode
    try {
        $barcode_data = @file_get_contents($barcode_url);
        
        if($barcode_data !== false) {
            file_put_contents($filepath, $barcode_data);
            return 'uploads/barcodes/' . $filename;
        } else {
            // Fallback: generate simple text-based barcode
            return generateTextBarcode($code);
        }
    } catch(Exception $e) {
        // Fallback to text barcode if API fails
        return generateTextBarcode($code);
    }
}

/**
 * Fallback function to generate a simple text-based barcode image
 */
function generateTextBarcode($code) {
    $barcode_dir = '../uploads/barcodes/';
    if(!file_exists($barcode_dir)) {
        mkdir($barcode_dir, 0777, true);
    }
    
    $filename = $code . '_text.png';
    $filepath = $barcode_dir . $filename;
    
    // Create simple image with text
    $width = 300;
    $height = 100;
    $image = imagecreate($width, $height);
    
    // Colors
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    
    // Fill background
    imagefill($image, 0, 0, $white);
    
    // Add text
    imagestring($image, 5, 10, 40, $code, $black);
    
    // Save image
    imagepng($image, $filepath);
    imagedestroy($image);
    
    return 'uploads/barcodes/' . $filename;
}
?>