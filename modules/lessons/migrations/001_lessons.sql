-- Lessons Manager: τμήματα, μαθητές, παρουσίες, δίδακτρα

CREATE TABLE IF NOT EXISTS ls_groups (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  teacher VARCHAR(120) NULL,
  room VARCHAR(60) NULL,
  schedule VARCHAR(190) NULL,
  weekdays VARCHAR(20) NULL,
  start_time TIME NULL,
  end_time TIME NULL,
  capacity SMALLINT UNSIGNED NULL,
  monthly_fee_cents INT UNSIGNED NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ls_students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  parent_name VARCHAR(120) NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  notes VARCHAR(500) NULL,
  token CHAR(24) NOT NULL UNIQUE,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz (business_id),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ls_enrollments (
  group_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  fee_cents INT UNSIGNED NULL,
  since DATE NOT NULL,
  PRIMARY KEY (group_id, student_id),
  FOREIGN KEY (group_id) REFERENCES ls_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES ls_students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ls_attendance (
  group_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  day DATE NOT NULL,
  present TINYINT(1) NOT NULL,
  PRIMARY KEY (group_id, student_id, day),
  FOREIGN KEY (group_id) REFERENCES ls_groups(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES ls_students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Χρεώσεις διδάκτρων ανά μήνα και πληρωμές
CREATE TABLE IF NOT EXISTS ls_charges (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  month CHAR(7) NOT NULL,
  description VARCHAR(190) NOT NULL,
  amount_cents INT UNSIGNED NOT NULL,
  paid_cents INT UNSIGNED NOT NULL DEFAULT 0,
  paid_at DATE NULL,
  UNIQUE KEY uq_student_month_desc (student_id, month, description),
  KEY k_biz_month (business_id, month),
  FOREIGN KEY (student_id) REFERENCES ls_students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
