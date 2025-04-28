-- jestli uz existuje vymaz
DROP TABLE IF EXISTS `todos`;
DROP TABLE IF EXISTS `users`;

-- 1) Tabulka uzivatele
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email`    VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- 2) Tabulka To-Do polozek
CREATE TABLE `todos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id`      INT          NOT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `description`  TEXT,
  `is_completed` TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     NOT NULL
                    DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_todos_user` (`user_id`),
  CONSTRAINT `fk_todos_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;
