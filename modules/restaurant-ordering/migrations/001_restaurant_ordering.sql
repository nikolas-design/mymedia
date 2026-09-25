-- Restaurant Ordering: κατάλογος, ρυθμίσεις, παραγγελίες delivery / take away

CREATE TABLE IF NOT EXISTS ro_settings (
  business_id INT UNSIGNED PRIMARY KEY,
  code VARCHAR(16) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#c2410c',
  accepting TINYINT(1) NOT NULL DEFAULT 1,
  delivery TINYINT(1) NOT NULL DEFAULT 1,
  takeaway TINYINT(1) NOT NULL DEFAULT 1,
  min_order_cents INT UNSIGNED NOT NULL DEFAULT 1000,
  delivery_fee_cents INT UNSIGNED NOT NULL DEFAULT 0,
  areas VARCHAR(500) NULL,
  prep_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  phone VARCHAR(40) NULL,
  address VARCHAR(190) NULL,
  hours VARCHAR(190) NULL,
  note VARCHAR(255) NULL,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ro_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ro_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(400) NULL,
  price_cents INT UNSIGNED NOT NULL,
  photo VARCHAR(190) NULL,
  available TINYINT(1) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0,
  KEY k_cat (category_id),
  FOREIGN KEY (category_id) REFERENCES ro_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ro_orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  number INT UNSIGNED NOT NULL,
  token CHAR(32) NOT NULL UNIQUE,
  kind ENUM('delivery','takeaway') NOT NULL,
  status ENUM('new','accepted','ready','out','completed','rejected') NOT NULL DEFAULT 'new',
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address VARCHAR(255) NULL,
  floor_bell VARCHAR(120) NULL,
  notes VARCHAR(500) NULL,
  payment ENUM('cash','card') NOT NULL DEFAULT 'cash',
  subtotal_cents INT UNSIGNED NOT NULL,
  fee_cents INT UNSIGNED NOT NULL DEFAULT 0,
  total_cents INT UNSIGNED NOT NULL,
  eta_minutes SMALLINT UNSIGNED NULL,
  reject_reason VARCHAR(190) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  accepted_at DATETIME NULL,
  KEY k_biz_time (business_id, created_at),
  KEY k_biz_status (business_id, status),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ro_order_lines (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  item_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  qty SMALLINT UNSIGNED NOT NULL,
  price_cents INT UNSIGNED NOT NULL,
  note VARCHAR(190) NULL,
  FOREIGN KEY (order_id) REFERENCES ro_orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
