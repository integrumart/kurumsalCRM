<?php
require_once 'config.php';
$page_title = 'Görev Düzenle';
include 'header.php';

$success = '';
$error = '';
$task = null;

// Görev bilgilerini getir
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    $stmt->close();
    
    if (!$task) {
        redirect('tasks.php');
    }
} else {
    redirect('tasks.php');
}

// Müşterileri ve kullanıcıları getir
$customers = $conn->query("SELECT id, company_name FROM customers ORDER BY company_name");
$users = $conn->query("SELECT id, full_name FROM users WHERE status = 'active' ORDER BY full_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    $assigned_to = intval($_POST['assigned_to']);
    $priority = sanitize($_POST['priority']);
    $status = sanitize($_POST['status']);
    $due_date = sanitize($_POST['due_date']);
    
    if (!empty($title) && $assigned_to > 0) {
        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, customer_id=?, assigned_to=?, priority=?, status=?, due_date=? WHERE id=?");
        $stmt->bind_param("ssiisssi", $title, $description, $customer_id, $assigned_to, $priority, $status, $due_date, $id);
        
        if ($stmt->execute()) {
            logActivity($_SESSION['user_id'], 'update', 'task', $id, "Görev güncellendi: $title");
            $success = 'Görev başarıyla güncellendi!';
            // Güncel bilgileri tekrar çek
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $task = $stmt->get_result()->fetch_assoc();
        } else {
            $error = 'Görev güncellenirken bir hata oluştu!';
        }
        $stmt->close();
    } else {
        $error = 'Başlık ve atanan kullanıcı zorunludur!';
    }
}
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-pencil"></i> Görev Düzenle</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="tasks.php" class="btn btn-secondary">
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
                <div class="col-md-12 mb-3">
                    <label class="form-label">Görev Başlığı <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Açıklama</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Müşteri</label>
                    <select class="form-select" name="customer_id">
                        <option value="">Seçin...</option>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $customer['id']; ?>" <?php echo $customer['id'] == $task['customer_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($customer['company_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Atanan Kullanıcı <span class="text-danger">*</span></label>
                    <select class="form-select" name="assigned_to" required>
                        <option value="">Seçin...</option>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo $user['id'] == $task['assigned_to'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Öncelik</label>
                    <select class="form-select" name="priority">
                        <option value="düşük" <?php echo $task['priority'] == 'düşük' ? 'selected' : ''; ?>>Düşük</option>
                        <option value="normal" <?php echo $task['priority'] == 'normal' ? 'selected' : ''; ?>>Normal</option>
                        <option value="yüksek" <?php echo $task['priority'] == 'yüksek' ? 'selected' : ''; ?>>Yüksek</option>
                        <option value="acil" <?php echo $task['priority'] == 'acil' ? 'selected' : ''; ?>>Acil</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Durum</label>
                    <select class="form-select" name="status">
                        <option value="bekliyor" <?php echo $task['status'] == 'bekliyor' ? 'selected' : ''; ?>>Bekliyor</option>
                        <option value="devam_ediyor" <?php echo $task['status'] == 'devam_ediyor' ? 'selected' : ''; ?>>Devam Ediyor</option>
                        <option value="tamamlandı" <?php echo $task['status'] == 'tamamlandı' ? 'selected' : ''; ?>>Tamamlandı</option>
                        <option value="iptal" <?php echo $task['status'] == 'iptal' ? 'selected' : ''; ?>>İptal</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Son Tarih</label>
                    <input type="date" class="form-control" name="due_date" value="<?php echo $task['due_date']; ?>">
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Güncelle
                </button>
                <a href="tasks.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
