<?php
require_once 'config.php';
$page_title = 'Raporlar';
include 'header.php';

// Satış İstatistikleri
$sales_stats = [];

// Durumlara göre satış sayısı
$result = $conn->query("SELECT status, COUNT(*) as count, SUM(amount) as total FROM sales GROUP BY status");
while ($row = $result->fetch_assoc()) {
    $sales_stats[$row['status']] = [
        'count' => $row['count'],
        'total' => $row['total'] ?? 0
    ];
}

// Toplam satış tutarı (kazanılan)
$result = $conn->query("SELECT SUM(amount) as total FROM sales WHERE status = 'kazanıldı'");
$won_sales_total = $result->fetch_assoc()['total'] ?? 0;

// Potansiyel satış tutarı
$result = $conn->query("SELECT SUM(amount) as total FROM sales WHERE status IN ('yeni', 'görüşme', 'teklif')");
$potential_sales_total = $result->fetch_assoc()['total'] ?? 0;

// Aylık satışlar (son 6 ay)
$monthly_sales = $conn->query("SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as count,
    SUM(amount) as total
    FROM sales 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC");

// En iyi müşteriler (en çok satış yapılan)
$top_customers = $conn->query("SELECT 
    c.company_name,
    COUNT(s.id) as sale_count,
    SUM(s.amount) as total_amount
    FROM customers c
    INNER JOIN sales s ON c.id = s.customer_id
    WHERE s.status = 'kazanıldı'
    GROUP BY c.id
    ORDER BY total_amount DESC
    LIMIT 5");

// Görev İstatistikleri
$task_stats = $conn->query("SELECT status, COUNT(*) as count FROM tasks GROUP BY status");
$task_status = [];
while ($row = $task_stats->fetch_assoc()) {
    $task_status[$row['status']] = $row['count'];
}

// Kullanıcı Performansı
$user_performance = $conn->query("SELECT 
    u.full_name,
    COUNT(DISTINCT s.id) as sale_count,
    COUNT(DISTINCT t.id) as task_count,
    SUM(CASE WHEN s.status = 'kazanıldı' THEN s.amount ELSE 0 END) as total_won
    FROM users u
    LEFT JOIN sales s ON u.id = s.assigned_to
    LEFT JOIN tasks t ON u.id = t.assigned_to
    WHERE u.status = 'active'
    GROUP BY u.id
    ORDER BY total_won DESC");
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="bi bi-graph-up"></i> Raporlar ve İstatistikler</h1>
    </div>
</div>

<!-- Satış Özeti -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Satış Özeti</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="stat-box text-center">
                            <h2 class="text-success"><?php echo formatCurrency($won_sales_total); ?></h2>
                            <p class="text-muted mb-0">Kazanılan Satışlar</p>
                            <small class="text-muted">
                                <?php echo isset($sales_stats['kazanıldı']) ? $sales_stats['kazanıldı']['count'] : 0; ?> adet
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-box text-center">
                            <h2 class="text-warning"><?php echo formatCurrency($potential_sales_total); ?></h2>
                            <p class="text-muted mb-0">Potansiyel Satışlar</p>
                            <small class="text-muted">
                                <?php 
                                $potential_count = 0;
                                $potential_count += isset($sales_stats['yeni']) ? $sales_stats['yeni']['count'] : 0;
                                $potential_count += isset($sales_stats['görüşme']) ? $sales_stats['görüşme']['count'] : 0;
                                $potential_count += isset($sales_stats['teklif']) ? $sales_stats['teklif']['count'] : 0;
                                echo $potential_count;
                                ?> adet
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-box text-center">
                            <h2 class="text-info">
                                <?php 
                                $total_sales_count = array_sum(array_column($sales_stats, 'count'));
                                echo $total_sales_count;
                                ?>
                            </h2>
                            <p class="text-muted mb-0">Toplam Fırsat</p>
                            <small class="text-muted">Tüm durumlar</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="mb-3">Durumlara Göre Dağılım</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Durum</th>
                                <th>Adet</th>
                                <th>Toplam Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales_stats as $status => $data): ?>
                            <tr>
                                <td><?php echo getStatusBadge($status, 'sale'); ?></td>
                                <td><?php echo $data['count']; ?></td>
                                <td><?php echo formatCurrency($data['total']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aylık Satışlar -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar3"></i> Son 6 Ay Satışlar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Ay</th>
                                <th>Adet</th>
                                <th>Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($monthly_sales->num_rows > 0): ?>
                                <?php while ($row = $monthly_sales->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                                    <td><?php echo $row['count']; ?></td>
                                    <td><strong><?php echo formatCurrency($row['total']); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Veri bulunamadı</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-star"></i> En İyi Müşteriler</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Müşteri</th>
                                <th>Satış Adedi</th>
                                <th>Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($top_customers->num_rows > 0): ?>
                                <?php while ($row = $top_customers->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['company_name']); ?></strong></td>
                                    <td><?php echo $row['sale_count']; ?></td>
                                    <td><strong><?php echo formatCurrency($row['total_amount']); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Veri bulunamadı</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Görev İstatistikleri -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-check2-square"></i> Görev İstatistikleri</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Durum</th>
                                <th>Adet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($task_status as $status => $count): ?>
                            <tr>
                                <td><?php echo getStatusBadge($status, 'task'); ?></td>
                                <td><strong><?php echo $count; ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Kullanıcı Performansı</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>Satış</th>
                                <th>Görev</th>
                                <th>Kazanılan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($user_performance->num_rows > 0): ?>
                                <?php while ($row = $user_performance->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                                    <td><?php echo $row['sale_count']; ?></td>
                                    <td><?php echo $row['task_count']; ?></td>
                                    <td><strong><?php echo formatCurrency($row['total_won']); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Veri bulunamadı</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
