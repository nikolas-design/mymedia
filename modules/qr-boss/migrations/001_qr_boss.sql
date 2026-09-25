-- QR Boss: δυναμικά QR, ψηφιακό μενού, στατιστικά, κλήση σερβιτόρου

-- Εμφάνιση του μενού ανά επιχείρηση
CREATE TABLE IF NOT EXISTS qr_profiles (
  business_id INT UNSIGNED PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  subtitle VARCHAR(190) NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#793de7',
  logo VARCHAR(190) NULL,
  cover VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  address VARCHAR(190) NULL,
  hours VARCHAR(190) NULL,
  wifi_ssid VARCHAR(64) NULL,
  wifi_pass VARCHAR(64) NULL,
  instagram VARCHAR(190) NULL,
  facebook VARCHAR(190) NULL,
  google_review_url VARCHAR(255) NULL,
  footer VARCHAR(255) NULL,
  waiter_calls TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS qr_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  note VARCHAR(190) NULL,
  sort INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  KEY k_biz (business_id, sort),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS qr_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(400) NULL,
  price_cents INT UNSIGNED NULL,
  photo VARCHAR(190) NULL,
  tags VARCHAR(120) NULL,
  available TINYINT(1) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0,
  KEY k_cat (category_id, sort),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES qr_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Κάθε QR: ο σύντομος κωδικός δεν αλλάζει ποτέ, ο προορισμός αλλάζει όποτε θέλεις
CREATE TABLE IF NOT EXISTS qr_codes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  code VARCHAR(16) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  type ENUM('menu','link','wifi','review') NOT NULL DEFAULT 'menu',
  target_url VARCHAR(500) NULL,
  table_label VARCHAR(40) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Σαρώσεις: χωρίς IP ή άλλα προσωπικά δεδομένα
CREATE TABLE IF NOT EXISTS qr_scans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  qr_id INT UNSIGNED NOT NULL,
  device ENUM('mobile','desktop') NOT NULL DEFAULT 'mobile',
  scanned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz_time (business_id, scanned_at),
  KEY k_qr_time (qr_id, scanned_at),
  FOREIGN KEY (qr_id) REFERENCES qr_codes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Κλήσεις από τα τραπέζια (Pro)
CREATE TABLE IF NOT EXISTS qr_calls (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  qr_id INT UNSIGNED NULL,
  table_label VARCHAR(40) NOT NULL,
  kind ENUM('waiter','bill') NOT NULL DEFAULT 'waiter',
  status ENUM('new','done') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  done_at DATETIME NULL,
  done_by INT UNSIGNED NULL,
  KEY k_biz_status (business_id, status),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
