<?php
require_once 'config.php';
$page_title = 'Müşteri Düzenle';
include 'header.php';

$success = '';
$error = '';
$customer = null;

// Müşteri bilgilerini getir
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();
    
    if (!$customer) {
        redirect('customers.php');
    }
} else {
    redirect('customers.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = sanitize($_POST['company_name']);
    $contact_person = sanitize($_POST['contact_person']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $mobile = sanitize($_POST['mobile']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $country = sanitize($_POST['country']);
    $tax_number = sanitize($_POST['tax_number']);
    $industry = sanitize($_POST['industry']);
    $website = sanitize($_POST['website']);
    $customer_type = sanitize($_POST['customer_type']);
    $notes = sanitize($_POST['notes']);
    
    if (!empty($company_name)) {
        $stmt = $conn->prepare("UPDATE customers SET company_name=?, contact_person=?, email=?, phone=?, mobile=?, address=?, city=?, country=?, tax_number=?, industry=?, website=?, customer_type=?, notes=? WHERE id=?");
        $stmt->bind_param("sssssssssssssi", $company_name, $contact_person, $email, $phone, $mobile, $address, $city, $country, $tax_number, $industry, $website, $customer_type, $notes, $id);
        
        if ($stmt->execute()) {
            logActivity($_SESSION['user_id'], 'update', 'customer', $id, "Müşteri güncellendi: $company_name");
            $success = 'Müşteri başarıyla güncellendi!';
            // Güncel bilgileri tekrar çek
            $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $customer = $stmt->get_result()->fetch_assoc();
        } else {
            $error = 'Müşteri güncellenirken bir hata oluştu!';
        }
        $stmt->close();
    } else {
        $error = 'Şirket adı zorunludur!';
    }
}
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-pencil"></i> Müşteri Düzenle</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="customers.php" class="btn btn-secondary">
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
                <div class="col-md-6 mb-3">
                    <label class="form-label">Şirket Adı <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="company_name" value="<?php echo htmlspecialchars($customer['company_name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">İlgili Kişi</label>
                    <input type="text" class="form-control" name="contact_person" value="<?php echo htmlspecialchars($customer['contact_person']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">E-posta</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Mobil</label>
                    <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($customer['mobile']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Adres</label>
                    <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Şehir</label>
                    <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ülke</label>
                    <input type="text" class="form-control" name="country" value="<?php echo htmlspecialchars($customer['country']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Vergi No</label>
                    <input type="text" class="form-control" name="tax_number" value="<?php echo htmlspecialchars($customer['tax_number']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sektör</label>
                    <input type="text" class="form-control" name="industry" value="<?php echo htmlspecialchars($customer['industry']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Web Sitesi</label>
                    <input type="text" class="form-control" name="website" value="<?php echo htmlspecialchars($customer['website']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Müşteri Tipi</label>
                    <select class="form-select" name="customer_type">
                        <option value="potansiyel" <?php echo $customer['customer_type'] == 'potansiyel' ? 'selected' : ''; ?>>Potansiyel</option>
                        <option value="aktif" <?php echo $customer['customer_type'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="pasif" <?php echo $customer['customer_type'] == 'pasif' ? 'selected' : ''; ?>>Pasif</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notlar</label>
                    <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($customer['notes']); ?></textarea>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Güncelle
                </button>
                <a href="customers.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
