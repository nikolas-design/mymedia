-- Review Booster: αξιολογήσεις πελατών, προώθηση στο Google, ιδιωτικό feedback

CREATE TABLE IF NOT EXISTS rb_locations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  code VARCHAR(16) NOT NULL UNIQUE,
  google_url VARCHAR(500) NULL,
  threshold TINYINT UNSIGNED NOT NULL DEFAULT 4,
  question VARCHAR(190) NULL,
  thanks VARCHAR(255) NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#793de7',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Κάθε αξιολόγηση με αστέρια (χωρίς προσωπικά δεδομένα)
CREATE TABLE IF NOT EXISTS rb_ratings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED NOT NULL,
  stars TINYINT UNSIGNED NOT NULL,
  went_google TINYINT(1) NOT NULL DEFAULT 0,
  source ENUM('qr','email','link') NOT NULL DEFAULT 'qr',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz_time (business_id, created_at),
  FOREIGN KEY (location_id) REFERENCES rb_locations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ιδιωτικά σχόλια από δυσαρεστημένους πελάτες
CREATE TABLE IF NOT EXISTS rb_feedback (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED NOT NULL,
  rating_id INT UNSIGNED NULL,
  stars TINYINT UNSIGNED NOT NULL,
  message TEXT NOT NULL,
  name VARCHAR(120) NULL,
  contact VARCHAR(190) NULL,
  status ENUM('new','resolved') NOT NULL DEFAULT 'new',
  note TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_at DATETIME NULL,
  KEY k_biz_status (business_id, status),
  FOREIGN KEY (location_id) REFERENCES rb_locations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Αιτήματα αξιολόγησης που στάλθηκαν με email
CREATE TABLE IF NOT EXISTS rb_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NULL,
  email VARCHAR(190) NOT NULL,
  sent_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz (business_id, created_at),
  FOREIGN KEY (location_id) REFERENCES rb_locations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Έτοιμες απαντήσεις για κριτικές Google (Pro)
CREATE TABLE IF NOT EXISTS rb_templates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  title VARCHAR(120) NOT NULL,
  body TEXT NOT NULL,
  kind ENUM('positive','neutral','negative') NOT NULL DEFAULT 'positive',
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
