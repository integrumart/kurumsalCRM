<?php
require_once 'config.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Müşteriyi sil
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logActivity($_SESSION['user_id'], 'delete', 'customer', $id, "Müşteri silindi");
    }
    $stmt->close();
}

redirect('customers.php');
?>
