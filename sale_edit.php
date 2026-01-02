<?php
require_once 'config.php';
$page_title = 'Satış Düzenle';
include 'header.php';

$success = '';
$error = '';
$sale = null;

// Satış bilgilerini getir
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sale = $result->fetch_assoc();
    $stmt->close();
    
    if (!$sale) {
        redirect('sales.php');
    }
} else {
    redirect('sales.php');
}

// Müşterileri ve kullanıcıları getir
$customers = $conn->query("SELECT id, company_name FROM customers ORDER BY company_name");
$users = $conn->query("SELECT id, full_name FROM users WHERE status = 'active' ORDER BY full_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $amount = floatval($_POST['amount']);
    $currency = sanitize($_POST['currency']);
    $status = sanitize($_POST['status']);
    $probability = intval($_POST['probability']);
    $expected_close_date = sanitize($_POST['expected_close_date']);
    $assigned_to = intval($_POST['assigned_to']);
    
    if (!empty($title) && $customer_id > 0) {
        $stmt = $conn->prepare("UPDATE sales SET customer_id=?, title=?, description=?, amount=?, currency=?, status=?, probability=?, expected_close_date=?, assigned_to=? WHERE id=?");
        $stmt->bind_param("issdssissi", $customer_id, $title, $description, $amount, $currency, $status, $probability, $expected_close_date, $assigned_to, $id);
        
        if ($stmt->execute()) {
            logActivity($_SESSION['user_id'], 'update', 'sale', $id, "Satış güncellendi: $title");
            $success = 'Satış başarıyla güncellendi!';
            // Güncel bilgileri tekrar çek
            $stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $sale = $stmt->get_result()->fetch_assoc();
        } else {
            $error = 'Satış güncellenirken bir hata oluştu!';
        }
        $stmt->close();
    } else {
        $error = 'Başlık ve müşteri seçimi zorunludur!';
    }
}
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-pencil"></i> Satış Düzenle</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="sales.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Başlık <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($sale['title']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Müşteri <span class="text-danger">*</span></label>
                    <select class="form-select" name="customer_id" required>
                        <option value="">Seçin...</option>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $customer['id']; ?>" <?php echo $customer['id'] == $sale['customer_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($customer['company_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Açıklama</label>
                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($sale['description']); ?></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tutar</label>
                    <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $sale['amount']; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Para Birimi</label>
                    <select class="form-select" name="currency">
                        <option value="TRY" <?php echo $sale['currency'] == 'TRY' ? 'selected' : ''; ?>>TRY</option>
                        <option value="USD" <?php echo $sale['currency'] == 'USD' ? 'selected' : ''; ?>>USD</option>
                        <option value="EUR" <?php echo $sale['currency'] == 'EUR' ? 'selected' : ''; ?>>EUR</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Durum</label>
                    <select class="form-select" name="status">
                        <option value="yeni" <?php echo $sale['status'] == 'yeni' ? 'selected' : ''; ?>>Yeni</option>
                        <option value="görüşme" <?php echo $sale['status'] == 'görüşme' ? 'selected' : ''; ?>>Görüşme</option>
                        <option value="teklif" <?php echo $sale['status'] == 'teklif' ? 'selected' : ''; ?>>Teklif</option>
                        <option value="kazanıldı" <?php echo $sale['status'] == 'kazanıldı' ? 'selected' : ''; ?>>Kazanıldı</option>
                        <option value="kaybedildi" <?php echo $sale['status'] == 'kaybedildi' ? 'selected' : ''; ?>>Kaybedildi</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Olasılık (%)</label>
                    <input type="number" class="form-control" name="probability" value="<?php echo $sale['probability']; ?>" min="0" max="100">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Beklenen Kapanış Tarihi</label>
                    <input type="date" class="form-control" name="expected_close_date" value="<?php echo $sale['expected_close_date']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Atanan Kullanıcı</label>
                    <select class="form-select" name="assigned_to">
                        <option value="">Seçin...</option>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo $user['id'] == $sale['assigned_to'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Güncelle
                </button>
                <a href="sales.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
