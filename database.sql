-- Kurumsal CRM Veritabanı Şeması
-- Bu dosyayı MySQL/MariaDB'de çalıştırarak veritabanını oluşturun

CREATE DATABASE IF NOT EXISTS kurumsal_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kurumsal_crm;

-- Kullanıcılar Tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'manager', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Müşteriler Tablosu
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    mobile VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50),
    tax_number VARCHAR(50),
    industry VARCHAR(100),
    website VARCHAR(200),
    customer_type ENUM('potansiyel', 'aktif', 'pasif') DEFAULT 'potansiyel',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ürünler/Hizmetler Tablosu
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(200) NOT NULL,
    product_code VARCHAR(50) UNIQUE,
    description TEXT,
    category VARCHAR(100),
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'TRY',
    stock_quantity INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Satışlar/Fırsatlar Tablosu
CREATE TABLE IF NOT EXISTS sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'TRY',
    status ENUM('yeni', 'görüşme', 'teklif', 'kazanıldı', 'kaybedildi') DEFAULT 'yeni',
    probability INT DEFAULT 50,
    expected_close_date DATE,
    assigned_to INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Satış Detayları (Ürünler)
CREATE TABLE IF NOT EXISTS sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    total_price DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Görevler Tablosu
CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    customer_id INT,
    sale_id INT,
    assigned_to INT NOT NULL,
    priority ENUM('düşük', 'normal', 'yüksek', 'acil') DEFAULT 'normal',
    status ENUM('bekliyor', 'devam_ediyor', 'tamamlandı', 'iptal') DEFAULT 'bekliyor',
    due_date DATE,
    completed_at TIMESTAMP NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Aktiviteler/Log Tablosu
CREATE TABLE IF NOT EXISTS activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayılan Admin Kullanıcısı (şifre: admin123)
INSERT INTO users (username, password, full_name, email, role, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Kullanıcı', 'admin@example.com', 'admin', 'active');

-- Örnek Veri
INSERT INTO customers (company_name, contact_person, email, phone, city, country, customer_type, created_by) VALUES
('ABC Teknoloji A.Ş.', 'Ahmet Yılmaz', 'ahmet@abctek.com', '0212 555 0001', 'İstanbul', 'Türkiye', 'aktif', 1),
('XYZ Danışmanlık Ltd.', 'Ayşe Demir', 'ayse@xyzdans.com', '0216 555 0002', 'İstanbul', 'Türkiye', 'potansiyel', 1),
('DEF Ticaret A.Ş.', 'Mehmet Kaya', 'mehmet@deftic.com', '0312 555 0003', 'Ankara', 'Türkiye', 'aktif', 1);

INSERT INTO products (product_name, product_code, description, category, unit_price, currency, stock_quantity) VALUES
('CRM Yazılımı - Temel Paket', 'CRM-001', 'Temel CRM yazılım paketi', 'Yazılım', 5000.00, 'TRY', 100),
('CRM Yazılımı - Profesyonel', 'CRM-002', 'Profesyonel CRM yazılım paketi', 'Yazılım', 10000.00, 'TRY', 100),
('Danışmanlık Hizmeti', 'DNS-001', 'Saatlik danışmanlık hizmeti', 'Hizmet', 500.00, 'TRY', 999),
('Eğitim Paketi', 'EGT-001', 'Kullanıcı eğitim paketi', 'Eğitim', 2000.00, 'TRY', 50);

INSERT INTO sales (customer_id, title, description, amount, status, probability, expected_close_date, assigned_to, created_by) VALUES
(1, 'CRM Yazılım Satışı', 'ABC Teknoloji için CRM yazılım teklifi', 15000.00, 'teklif', 75, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 1, 1),
(2, 'Danışmanlık Projesi', 'XYZ Danışmanlık için süreç danışmanlığı', 25000.00, 'görüşme', 50, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 1),
(3, 'CRM Uygulama + Eğitim', 'DEF Ticaret için tam paket', 35000.00, 'yeni', 30, DATE_ADD(CURDATE(), INTERVAL 45 DAY), 1, 1);

INSERT INTO tasks (title, description, customer_id, assigned_to, priority, status, due_date, created_by) VALUES
('Müşteri görüşmesi yap', 'ABC Teknoloji ile teklif görüşmesi', 1, 1, 'yüksek', 'bekliyor', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 1),
('Teklif hazırla', 'XYZ Danışmanlık için detaylı teklif hazırla', 2, 1, 'normal', 'devam_ediyor', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 1),
('Demo sunumu', 'DEF Ticaret için demo sunumu organize et', 3, 1, 'yüksek', 'bekliyor', DATE_ADD(CURDATE(), INTERVAL 5 DAY), 1);
