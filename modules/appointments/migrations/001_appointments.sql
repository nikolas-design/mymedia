-- Appointments: υπηρεσίες, συνεργάτες, ωράρια, κρατήσεις

CREATE TABLE IF NOT EXISTS ap_settings (
  business_id INT UNSIGNED PRIMARY KEY,
  code VARCHAR(16) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  intro VARCHAR(255) NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#d64b7c',
  slot_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 15,
  min_notice_hours SMALLINT UNSIGNED NOT NULL DEFAULT 2,
  max_days_ahead SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  auto_confirm TINYINT(1) NOT NULL DEFAULT 1,
  cancel_hours SMALLINT UNSIGNED NOT NULL DEFAULT 12,
  phone VARCHAR(40) NULL,
  address VARCHAR(190) NULL,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ap_services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(255) NULL,
  duration_min SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  price_cents INT UNSIGNED NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ap_staff (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#793de7',
  active TINYINT(1) NOT NULL DEFAULT 1,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ποιος κάνει ποια υπηρεσία
CREATE TABLE IF NOT EXISTS ap_staff_services (
  staff_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (staff_id, service_id),
  FOREIGN KEY (staff_id) REFERENCES ap_staff(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES ap_services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ωράριο ανά ημέρα εβδομάδας (1=Δευτέρα). Πολλές γραμμές = διάλειμμα ανάμεσα.
CREATE TABLE IF NOT EXISTS ap_hours (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  staff_id INT UNSIGNED NOT NULL,
  weekday TINYINT UNSIGNED NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  KEY k_staff (staff_id, weekday),
  FOREIGN KEY (staff_id) REFERENCES ap_staff(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ap_timeoff (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  staff_id INT UNSIGNED NOT NULL,
  day_from DATE NOT NULL,
  day_to DATE NOT NULL,
  reason VARCHAR(120) NULL,
  FOREIGN KEY (staff_id) REFERENCES ap_staff(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ap_customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NULL,
  notes VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_phone (business_id, phone),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ap_bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  staff_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NULL,
  customer_id INT UNSIGNED NOT NULL,
  service_name VARCHAR(120) NOT NULL,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NOT NULL,
  price_cents INT UNSIGNED NULL,
  status ENUM('pending','confirmed','done','cancelled','noshow') NOT NULL DEFAULT 'confirmed',
  source ENUM('online','manual') NOT NULL DEFAULT 'online',
  notes VARCHAR(500) NULL,
  token CHAR(32) NOT NULL UNIQUE,
  reminded_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_staff_time (staff_id, starts_at),
  KEY k_biz_time (business_id, starts_at),
  FOREIGN KEY (staff_id) REFERENCES ap_staff(id) ON DELETE CASCADE,
  FOREIGN KEY (customer_id) REFERENCES ap_customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
