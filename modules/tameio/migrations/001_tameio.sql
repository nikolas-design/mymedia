-- Ταμείο: κλεισίματα βάρδιας και έξοδα

CREATE TABLE IF NOT EXISTS tm_closings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  day DATE NOT NULL,
  shift VARCHAR(40) NOT NULL DEFAULT 'Ημέρα',
  opening_cents INT NOT NULL DEFAULT 0,
  cash_cents INT NOT NULL DEFAULT 0,
  card_cents INT NOT NULL DEFAULT 0,
  other_cents INT NOT NULL DEFAULT 0,
  cash_expenses_cents INT NOT NULL DEFAULT 0,
  counted_cents INT NULL,
  notes VARCHAR(500) NULL,
  closed_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_day_shift (business_id, day, shift),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tm_expenses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  day DATE NOT NULL,
  category VARCHAR(40) NOT NULL,
  description VARCHAR(190) NULL,
  amount_cents INT UNSIGNED NOT NULL,
  payment ENUM('cash','card','bank') NOT NULL DEFAULT 'cash',
  photo VARCHAR(190) NULL,
  created_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz_day (business_id, day),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
