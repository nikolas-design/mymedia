-- Βάρδιες: προσωπικό, πρόγραμμα, αιτήματα

CREATE TABLE IF NOT EXISTS sh_people (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  position VARCHAR(60) NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  weekly_hours DECIMAL(5,1) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sh_shifts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  person_id INT UNSIGNED NOT NULL,
  day DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  position VARCHAR(60) NULL,
  note VARCHAR(190) NULL,
  KEY k_biz_day (business_id, day),
  FOREIGN KEY (person_id) REFERENCES sh_people(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sh_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  person_id INT UNSIGNED NOT NULL,
  kind ENUM('leave','off','swap') NOT NULL DEFAULT 'off',
  day_from DATE NOT NULL,
  day_to DATE NOT NULL,
  note VARCHAR(500) NULL,
  status ENUM('new','approved','rejected') NOT NULL DEFAULT 'new',
  decided_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz_status (business_id, status),
  FOREIGN KEY (person_id) REFERENCES sh_people(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Δημοσιευμένες εβδομάδες και σύνδεσμος για την ομάδα
CREATE TABLE IF NOT EXISTS sh_settings (
  business_id INT UNSIGNED PRIMARY KEY,
  share_token VARCHAR(32) NOT NULL UNIQUE,
  published_until DATE NULL,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
