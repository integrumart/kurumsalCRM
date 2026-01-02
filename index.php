<?php
require_once 'config.php';
$page_title = 'Dashboard';
include 'header.php';

// İstatistikler
$stats = [];

// Toplam müşteri sayısı
$result = $conn->query("SELECT COUNT(*) as count FROM customers");
$stats['total_customers'] = $result->fetch_assoc()['count'];

// Aktif müşteri sayısı
$result = $conn->query("SELECT COUNT(*) as count FROM customers WHERE customer_type = 'aktif'");
$stats['active_customers'] = $result->fetch_assoc()['count'];

// Toplam satış tutarı
$result = $conn->query("SELECT SUM(amount) as total FROM sales WHERE status = 'kazanıldı'");
$stats['total_sales'] = $result->fetch_assoc()['total'] ?? 0;

// Bekleyen görev sayısı
$result = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status IN ('bekliyor', 'devam_ediyor')");
$stats['pending_tasks'] = $result->fetch_assoc()['count'];

// Bekleyen satışlar
$result = $conn->query("SELECT COUNT(*) as count FROM sales WHERE status IN ('yeni', 'görüşme', 'teklif')");
$stats['pending_sales'] = $result->fetch_assoc()['count'];

// Toplam ürün sayısı
$result = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'");
$stats['total_products'] = $result->fetch_assoc()['count'];

// Son eklenen müşteriler
$recent_customers = $conn->query("SELECT id, company_name, contact_person, customer_type, created_at FROM customers ORDER BY created_at DESC LIMIT 5");

// Yaklaşan görevler
$upcoming_tasks = $conn->query("SELECT t.*, c.company_name, u.full_name as assigned_name 
                                FROM tasks t 
                                LEFT JOIN customers c ON t.customer_id = c.id 
                                LEFT JOIN users u ON t.assigned_to = u.id 
                                WHERE t.status IN ('bekliyor', 'devam_ediyor') 
                                ORDER BY t.due_date ASC LIMIT 5");

// Son satış fırsatları
$recent_sales = $conn->query("SELECT s.*, c.company_name FROM sales s 
                              LEFT JOIN customers c ON s.customer_id = c.id 
                              ORDER BY s.created_at DESC LIMIT 5");
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p class="text-muted">Hoş geldiniz, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
    </div>
</div>

<!-- İstatistik Kartları -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Toplam Müşteri</h6>
                        <h2 class="mb-0"><?php echo $stats['total_customers']; ?></h2>
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i> <?php echo $stats['active_customers']; ?> Aktif
                        </small>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Toplam Satış</h6>
                        <h2 class="mb-0"><?php echo formatCurrency($stats['total_sales']); ?></h2>
                        <small class="text-warning">
                            <i class="bi bi-clock"></i> <?php echo $stats['pending_sales']; ?> Bekliyor
                        </small>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Bekleyen Görevler</h6>
                        <h2 class="mb-0"><?php echo $stats['pending_tasks']; ?></h2>
                        <small class="text-info">
                            <i class="bi bi-box-seam"></i> <?php echo $stats['total_products']; ?> Ürün
                        </small>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="bi bi-check2-square"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ana İçerik -->
<div class="row">
    <!-- Son Müşteriler -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people"></i> Son Eklenen Müşteriler</h5>
                <a href="customers.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Şirket</th>
                                <th>İlgili Kişi</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($customer = $recent_customers->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($customer['company_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($customer['contact_person']); ?></td>
                                <td><?php echo getStatusBadge($customer['customer_type'], 'customer'); ?></td>
                                <td><?php echo formatDate($customer['created_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Yaklaşan Görevler -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-check2-square"></i> Yaklaşan Görevler</h5>
                <a href="tasks.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Görev</th>
                                <th>Müşteri</th>
                                <th>Öncelik</th>
                                <th>Son Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($task = $upcoming_tasks->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($task['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($task['company_name'] ?? '-'); ?></td>
                                <td><?php echo getStatusBadge($task['priority'], 'priority'); ?></td>
                                <td><?php echo formatDate($task['due_date']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Son Satış Fırsatları -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Son Satış Fırsatları</h5>
                <a href="sales.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
            </div>
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
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($sale['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sale['company_name']); ?></td>
                                <td><?php echo formatCurrency($sale['amount'], $sale['currency']); ?></td>
                                <td><?php echo getStatusBadge($sale['status'], 'sale'); ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $sale['probability']; ?>%">
                                            <?php echo $sale['probability']; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo formatDate($sale['created_at']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
