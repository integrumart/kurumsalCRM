<?php
require_once 'config.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logActivity($_SESSION['user_id'], 'delete', 'product', $id, "Ürün silindi");
    }
    $stmt->close();
}

redirect('products.php');
?>
