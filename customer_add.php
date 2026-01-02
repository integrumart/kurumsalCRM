<?php
require_once 'config.php';
$page_title = 'Yeni Müşteri Ekle';
include 'header.php';

$success = '';
$error = '';

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
        $stmt = $conn->prepare("INSERT INTO customers (company_name, contact_person, email, phone, mobile, address, city, country, tax_number, industry, website, customer_type, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssssi", $company_name, $contact_person, $email, $phone, $mobile, $address, $city, $country, $tax_number, $industry, $website, $customer_type, $notes, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $customer_id = $stmt->insert_id;
            logActivity($_SESSION['user_id'], 'create', 'customer', $customer_id, "Yeni müşteri eklendi: $company_name");
            $success = 'Müşteri başarıyla eklendi!';
        } else {
            $error = 'Müşteri eklenirken bir hata oluştu!';
        }
        $stmt->close();
    } else {
        $error = 'Şirket adı zorunludur!';
    }
}
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-person-plus"></i> Yeni Müşteri Ekle</h1>
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
                    <input type="text" class="form-control" name="company_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">İlgili Kişi</label>
                    <input type="text" class="form-control" name="contact_person">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">E-posta</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="text" class="form-control" name="phone">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Mobil</label>
                    <input type="text" class="form-control" name="mobile">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Adres</label>
                    <textarea class="form-control" name="address" rows="2"></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Şehir</label>
                    <input type="text" class="form-control" name="city">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ülke</label>
                    <input type="text" class="form-control" name="country" value="Türkiye">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Vergi No</label>
                    <input type="text" class="form-control" name="tax_number">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sektör</label>
                    <input type="text" class="form-control" name="industry">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Web Sitesi</label>
                    <input type="text" class="form-control" name="website">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Müşteri Tipi</label>
                    <select class="form-select" name="customer_type">
                        <option value="potansiyel">Potansiyel</option>
                        <option value="aktif">Aktif</option>
                        <option value="pasif">Pasif</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notlar</label>
                    <textarea class="form-control" name="notes" rows="3"></textarea>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Kaydet
                </button>
                <a href="customers.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
