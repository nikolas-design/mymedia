-- Hotel Booking: τύποι δωματίων, τιμές, κλεισίματα, κρατήσεις

CREATE TABLE IF NOT EXISTS hb_settings (
  business_id INT UNSIGNED PRIMARY KEY,
  code VARCHAR(16) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#22a06b',
  checkin VARCHAR(10) NOT NULL DEFAULT '14:00',
  checkout VARCHAR(10) NOT NULL DEFAULT '11:00',
  min_nights TINYINT UNSIGNED NOT NULL DEFAULT 1,
  auto_confirm TINYINT(1) NOT NULL DEFAULT 0,
  deposit_percent TINYINT UNSIGNED NOT NULL DEFAULT 30,
  policy TEXT NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  address VARCHAR(190) NULL,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hb_rooms (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(500) NULL,
  capacity TINYINT UNSIGNED NOT NULL DEFAULT 2,
  units TINYINT UNSIGNED NOT NULL DEFAULT 1,
  base_price_cents INT UNSIGNED NOT NULL,
  photo VARCHAR(190) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Τιμή ανά βράδυ για περίοδο (υπερισχύει της βασικής)
CREATE TABLE IF NOT EXISTS hb_rates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  date_from DATE NOT NULL,
  date_to DATE NOT NULL,
  price_cents INT UNSIGNED NOT NULL,
  label VARCHAR(60) NULL,
  FOREIGN KEY (room_id) REFERENCES hb_rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Κλειστές ημέρες (π.χ. κρατήσεις από Booking, συντήρηση)
CREATE TABLE IF NOT EXISTS hb_blocks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  date_from DATE NOT NULL,
  date_to DATE NOT NULL,
  units TINYINT UNSIGNED NOT NULL DEFAULT 1,
  reason VARCHAR(120) NULL,
  FOREIGN KEY (room_id) REFERENCES hb_rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hb_bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  room_id INT UNSIGNED NOT NULL,
  token CHAR(32) NOT NULL UNIQUE,
  checkin DATE NOT NULL,
  checkout DATE NOT NULL,
  guests TINYINT UNSIGNED NOT NULL DEFAULT 2,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  country VARCHAR(60) NULL,
  notes VARCHAR(500) NULL,
  total_cents INT UNSIGNED NOT NULL,
  paid_cents INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('pending','confirmed','cancelled','checked_in','completed') NOT NULL DEFAULT 'pending',
  source ENUM('direct','phone','other') NOT NULL DEFAULT 'direct',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_room_dates (room_id, checkin, checkout),
  KEY k_biz (business_id, checkin),
  FOREIGN KEY (room_id) REFERENCES hb_rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
