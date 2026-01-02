<?php
require_once 'config.php';
$page_title = 'Müşteriler';
include 'header.php';

// Arama ve filtreleme
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : '';

$where = "1=1";
if (!empty($search)) {
    $where .= " AND (c.company_name LIKE '%$search%' OR c.contact_person LIKE '%$search%' OR c.email LIKE '%$search%')";
}
if (!empty($filter)) {
    $where .= " AND c.customer_type = '$filter'";
}

$customers = $conn->query("SELECT c.*, u.full_name as created_by_name 
                           FROM customers c 
                           LEFT JOIN users u ON c.created_by = u.id 
                           WHERE $where 
                           ORDER BY c.created_at DESC");
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-people"></i> Müşteriler</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="customer_add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Müşteri Ekle
        </a>
    </div>
</div>

<!-- Arama ve Filtreleme -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Şirket adı, kişi veya e-posta ara..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="filter">
                    <option value="">Tüm Müşteriler</option>
                    <option value="potansiyel" <?php echo $filter == 'potansiyel' ? 'selected' : ''; ?>>Potansiyel</option>
                    <option value="aktif" <?php echo $filter == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="pasif" <?php echo $filter == 'pasif' ? 'selected' : ''; ?>>Pasif</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Ara</button>
            </div>
        </form>
    </div>
</div>

<!-- Müşteri Listesi -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Şirket Adı</th>
                        <th>İlgili Kişi</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>Şehir</th>
                        <th>Durum</th>
                        <th>Oluşturma</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers->num_rows > 0): ?>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($customer['company_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($customer['contact_person'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($customer['city'] ?? '-'); ?></td>
                            <td><?php echo getStatusBadge($customer['customer_type'], 'customer'); ?></td>
                            <td><?php echo formatDate($customer['created_at']); ?></td>
                            <td>
                                <a href="customer_edit.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-warning" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="customer_delete.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete('Bu müşteriyi silmek istediğinizden emin misiniz?');" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox display-4 d-block text-muted mb-3"></i>
                                <p class="text-muted">Henüz müşteri eklenmemiş.</p>
                                <a href="customer_add.php" class="btn btn-primary">İlk Müşteriyi Ekle</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
