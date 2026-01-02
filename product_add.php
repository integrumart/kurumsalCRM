<?php
require_once 'config.php';
$page_title = 'Yeni Ürün Ekle';
include 'header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = sanitize($_POST['product_name']);
    $product_code = sanitize($_POST['product_code']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $unit_price = floatval($_POST['unit_price']);
    $currency = sanitize($_POST['currency']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $status = sanitize($_POST['status']);
    
    if (!empty($product_name)) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, product_code, description, category, unit_price, currency, stock_quantity, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdssi", $product_name, $product_code, $description, $category, $unit_price, $currency, $stock_quantity, $status);
        
        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;
            logActivity($_SESSION['user_id'], 'create', 'product', $product_id, "Yeni ürün eklendi: $product_name");
            $success = 'Ürün başarıyla eklendi!';
        } else {
            $error = 'Ürün eklenirken bir hata oluştu!';
        }
        $stmt->close();
    } else {
        $error = 'Ürün adı zorunludur!';
    }
}
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-plus-circle"></i> Yeni Ürün Ekle</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="products.php" class="btn btn-secondary">
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
                    <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="product_name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ürün Kodu</label>
                    <input type="text" class="form-control" name="product_code">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Açıklama</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" class="form-control" name="category">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Durum</label>
                    <select class="form-select" name="status">
                        <option value="active" selected>Aktif</option>
                        <option value="inactive">Pasif</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Birim Fiyat</label>
                    <input type="number" step="0.01" class="form-control" name="unit_price" value="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Para Birimi</label>
                    <select class="form-select" name="currency">
                        <option value="TRY" selected>TRY</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Miktarı</label>
                    <input type="number" class="form-control" name="stock_quantity" value="0">
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Kaydet
                </button>
                <a href="products.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
