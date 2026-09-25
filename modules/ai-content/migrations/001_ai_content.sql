-- AI Content: προφίλ ύφους, προτάσεις posts, ιστορικό παραγωγής

CREATE TABLE IF NOT EXISTS ai_profiles (
  business_id INT UNSIGNED PRIMARY KEY,
  description TEXT NULL,
  audience VARCHAR(255) NULL,
  tone VARCHAR(120) NULL,
  products TEXT NULL,
  avoid VARCHAR(255) NULL,
  hashtags VARCHAR(255) NULL,
  platforms VARCHAR(120) NOT NULL DEFAULT 'instagram,facebook',
  emojis TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ai_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  for_date DATE NULL,
  platform VARCHAR(20) NOT NULL DEFAULT 'instagram',
  title VARCHAR(120) NULL,
  caption TEXT NOT NULL,
  hashtags VARCHAR(500) NULL,
  image_idea VARCHAR(500) NULL,
  status ENUM('draft','approved','posted') NOT NULL DEFAULT 'draft',
  source ENUM('ai','manual') NOT NULL DEFAULT 'ai',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz_date (business_id, for_date),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Κάθε κλήση στο AI (για όριο ανά μήνα και κόστος)
CREATE TABLE IF NOT EXISTS ai_generations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  brief VARCHAR(500) NULL,
  posts INT UNSIGNED NOT NULL DEFAULT 0,
  model VARCHAR(60) NULL,
  input_tokens INT UNSIGNED NOT NULL DEFAULT 0,
  output_tokens INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('ok','error') NOT NULL DEFAULT 'ok',
  error VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY k_biz_time (business_id, created_at),
  FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
