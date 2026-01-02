<?php
require_once 'config.php';
$page_title = 'Ürünler';
include 'header.php';

// Arama ve filtreleme
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$where = "1=1";
if (!empty($search)) {
    $where .= " AND (product_name LIKE '%$search%' OR product_code LIKE '%$search%' OR category LIKE '%$search%')";
}

$products = $conn->query("SELECT * FROM products WHERE $where ORDER BY product_name");
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-box-seam"></i> Ürünler / Hizmetler</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="product_add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Ürün Ekle
        </a>
    </div>
</div>

<!-- Arama -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-9">
                <input type="text" class="form-control" name="search" placeholder="Ürün adı, kod veya kategori ara..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Ara</button>
            </div>
        </form>
    </div>
</div>

<!-- Ürün Listesi -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Ürün Adı</th>
                        <th>Ürün Kodu</th>
                        <th>Kategori</th>
                        <th>Birim Fiyat</th>
                        <th>Stok</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($product['product_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['product_code'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($product['category'] ?? '-'); ?></td>
                            <td><strong><?php echo formatCurrency($product['unit_price'], $product['currency']); ?></strong></td>
                            <td><?php echo $product['stock_quantity']; ?></td>
                            <td>
                                <?php if ($product['status'] == 'active'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="product_delete.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete('Bu ürünü silmek istediğinizden emin misiniz?');" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox display-4 d-block text-muted mb-3"></i>
                                <p class="text-muted">Henüz ürün eklenmemiş.</p>
                                <a href="product_add.php" class="btn btn-primary">İlk Ürünü Ekle</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
