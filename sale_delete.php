<?php
require_once 'config.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logActivity($_SESSION['user_id'], 'delete', 'sale', $id, "Satış silindi");
    }
    $stmt->close();
}

redirect('sales.php');
?>
