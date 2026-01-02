<?php
require_once 'config.php';
$page_title = 'Görevler';
include 'header.php';

// Filtreleme
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : '';

$where = "1=1";
if (!empty($filter)) {
    $where .= " AND t.status = '$filter'";
}

$tasks = $conn->query("SELECT t.*, c.company_name, u.full_name as assigned_name 
                       FROM tasks t 
                       LEFT JOIN customers c ON t.customer_id = c.id 
                       LEFT JOIN users u ON t.assigned_to = u.id 
                       WHERE $where 
                       ORDER BY t.due_date ASC, t.priority DESC");
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-check2-square"></i> Görevler</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="task_add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Görev Ekle
        </a>
    </div>
</div>

<!-- Filtreleme -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-9">
                <select class="form-select" name="filter">
                    <option value="">Tüm Görevler</option>
                    <option value="bekliyor" <?php echo $filter == 'bekliyor' ? 'selected' : ''; ?>>Bekliyor</option>
                    <option value="devam_ediyor" <?php echo $filter == 'devam_ediyor' ? 'selected' : ''; ?>>Devam Ediyor</option>
                    <option value="tamamlandı" <?php echo $filter == 'tamamlandı' ? 'selected' : ''; ?>>Tamamlandı</option>
                    <option value="iptal" <?php echo $filter == 'iptal' ? 'selected' : ''; ?>>İptal</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filtrele</button>
            </div>
        </form>
    </div>
</div>

<!-- Görev Listesi -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Görev</th>
                        <th>Müşteri</th>
                        <th>Atanan</th>
                        <th>Öncelik</th>
                        <th>Durum</th>
                        <th>Son Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tasks->num_rows > 0): ?>
                        <?php while ($task = $tasks->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($task['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($task['company_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($task['assigned_name']); ?></td>
                            <td><?php echo getStatusBadge($task['priority'], 'priority'); ?></td>
                            <td><?php echo getStatusBadge($task['status'], 'task'); ?></td>
                            <td><?php echo formatDate($task['due_date']); ?></td>
                            <td>
                                <a href="task_edit.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-warning" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="task_delete.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete('Bu görevi silmek istediğinizden emin misiniz?');" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox display-4 d-block text-muted mb-3"></i>
                                <p class="text-muted">Henüz görev eklenmemiş.</p>
                                <a href="task_add.php" class="btn btn-primary">İlk Görevi Ekle</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
