<?php
require_once 'config.php';
$page_title = 'Satışlar';
include 'header.php';

// Filtreleme
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : '';

$where = "1=1";
if (!empty($filter)) {
    $where .= " AND s.status = '$filter'";
}

$sales = $conn->query("SELECT s.*, c.company_name, u.full_name as assigned_name 
                       FROM sales s 
                       LEFT JOIN customers c ON s.customer_id = c.id 
                       LEFT JOIN users u ON s.assigned_to = u.id 
                       WHERE $where 
                       ORDER BY s.created_at DESC");
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-currency-dollar"></i> Satış Fırsatları</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="sale_add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Satış Ekle
        </a>
    </div>
</div>

<!-- Filtreleme -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-9">
                <select class="form-select" name="filter">
                    <option value="">Tüm Satışlar</option>
                    <option value="yeni" <?php echo $filter == 'yeni' ? 'selected' : ''; ?>>Yeni</option>
                    <option value="görüşme" <?php echo $filter == 'görüşme' ? 'selected' : ''; ?>>Görüşme</option>
                    <option value="teklif" <?php echo $filter == 'teklif' ? 'selected' : ''; ?>>Teklif</option>
                    <option value="kazanıldı" <?php echo $filter == 'kazanıldı' ? 'selected' : ''; ?>>Kazanıldı</option>
                    <option value="kaybedildi" <?php echo $filter == 'kaybedildi' ? 'selected' : ''; ?>>Kaybedildi</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filtrele</button>
            </div>
        </form>
    </div>
</div>

<!-- Satış Listesi -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Olasılık</th>
                        <th>Kapanış Tarihi</th>
                        <th>Atanan</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sales->num_rows > 0): ?>
                        <?php while ($sale = $sales->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($sale['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($sale['company_name']); ?></td>
                            <td><strong><?php echo formatCurrency($sale['amount'], $sale['currency']); ?></strong></td>
                            <td><?php echo getStatusBadge($sale['status'], 'sale'); ?></td>
                            <td>
                                <div class="progress" style="height: 20px; min-width: 80px;">
                                    <div class="progress-bar bg-<?php echo $sale['probability'] >= 75 ? 'success' : ($sale['probability'] >= 50 ? 'info' : 'warning'); ?>" 
                                         role="progressbar" style="width: <?php echo $sale['probability']; ?>%">
                                        <?php echo $sale['probability']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo formatDate($sale['expected_close_date']); ?></td>
                            <td><?php echo htmlspecialchars($sale['assigned_name'] ?? '-'); ?></td>
                            <td>
                                <a href="sale_edit.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-warning" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="sale_delete.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete('Bu satışı silmek istediğinizden emin misiniz?');" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox display-4 d-block text-muted mb-3"></i>
                                <p class="text-muted">Henüz satış fırsatı eklenmemiş.</p>
                                <a href="sale_add.php" class="btn btn-primary">İlk Satışı Ekle</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
