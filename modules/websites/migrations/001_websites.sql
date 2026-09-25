-- Websites: στοιχεία site πελάτη και αιτήματα αλλαγών

CREATE TABLE IF NOT EXISTS ws_sites (
  business_id INT UNSIGNED PRIMARY KEY,
  domain VARCHAR(190) NULL,
  site_url VARCHAR(255) NULL,
  platform VARCHAR(60) NULL,
  domain_until DATE NULL,
  hosting_until DATE NULL,
  ssl_until DATE NULL,
  changes_per_month TINYINT UNSIGNED NOT NULL DEFAULT 4,
  notes TEXT NULL,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ws_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  title VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  page_url VARCHAR(255) NULL,
  urgent TINYINT(1) NOT NULL DEFAULT 0,
  photo VARCHAR(190) NULL,
  status ENUM('new','in_progress','done','rejected') NOT NULL DEFAULT 'new',
  reply TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  KEY k_biz (business_id, created_at),
  KEY k_status (status),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
