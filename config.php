<?php
// Veritabanı Yapılandırması
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kurumsal_crm');

// Oturum başlat
session_start();

// Veritabanı bağlantısı
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");
    
    if ($conn->connect_error) {
        die("Veritabanı bağlantı hatası: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

// Yardımcı Fonksiyonlar
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars(strip_tags(trim($data))));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function getUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

function logActivity($user_id, $activity_type, $entity_type = null, $entity_id = null, $description = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activities (user_id, activity_type, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $user_id, $activity_type, $entity_type, $entity_id, $description);
    $stmt->execute();
    $stmt->close();
}

function formatCurrency($amount, $currency = 'TRY') {
    $symbol = ($currency == 'TRY') ? '₺' : $currency;
    return number_format($amount, 2, ',', '.') . ' ' . $symbol;
}

function formatDate($date) {
    if (empty($date)) return '-';
    return date('d.m.Y', strtotime($date));
}

function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    return date('d.m.Y H:i', strtotime($datetime));
}

function getStatusBadge($status, $type = 'default') {
    $badges = [
        'customer' => [
            'potansiyel' => 'warning',
            'aktif' => 'success',
            'pasif' => 'secondary'
        ],
        'sale' => [
            'yeni' => 'info',
            'görüşme' => 'primary',
            'teklif' => 'warning',
            'kazanıldı' => 'success',
            'kaybedildi' => 'danger'
        ],
        'task' => [
            'bekliyor' => 'secondary',
            'devam_ediyor' => 'primary',
            'tamamlandı' => 'success',
            'iptal' => 'danger'
        ],
        'priority' => [
            'düşük' => 'info',
            'normal' => 'secondary',
            'yüksek' => 'warning',
            'acil' => 'danger'
        ]
    ];
    
    $color = isset($badges[$type][$status]) ? $badges[$type][$status] : 'secondary';
    return "<span class='badge bg-{$color}'>{$status}</span>";
}
?>
