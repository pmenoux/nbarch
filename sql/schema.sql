-- nbarch.com — Schéma MySQL
-- v0.1 — 2026-03-02
-- PHP 8.x / MariaDB — Hébergement Infomaniak

SET NAMES utf8mb4;
SET time_zone = '+01:00';

-- ----------------------------
-- Catégories
-- ----------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`  VARCHAR(80)      NOT NULL,
  `nom`   VARCHAR(100)     NOT NULL,
  `ordre` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`slug`, `nom`, `ordre`) VALUES
  ('realisations',  'Réalisations',  1),
  ('scenographies', 'Scénographies', 2),
  ('boutiques',     'Boutiques',     3),
  ('concours',      'Concours',      4);

-- ----------------------------
-- Projets
-- ----------------------------
CREATE TABLE IF NOT EXISTS `projets` (
  `id`           INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  `categorie_id` TINYINT UNSIGNED  NOT NULL,
  `titre`        VARCHAR(200)      NOT NULL,
  `slug`         VARCHAR(220)      NOT NULL,
  `description`  TEXT,
  `annee`        SMALLINT UNSIGNED DEFAULT NULL,
  `lieu`         VARCHAR(150)      DEFAULT NULL,
  `statut`       ENUM('publié','brouillon') NOT NULL DEFAULT 'publié',
  `ordre`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`   DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_categorie` (`categorie_id`, `slug`),
  KEY `idx_categorie_statut_ordre` (`categorie_id`, `statut`, `ordre`),
  CONSTRAINT `fk_projets_categorie`
    FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Photos
-- La première photo (ordre = 0) fait office de couverture.
-- Le dossier physique : uploads/projets/{projet_id}/{filename}
-- ----------------------------
CREATE TABLE IF NOT EXISTS `photos` (
  `id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `projet_id` INT UNSIGNED  NOT NULL,
  `filename`  VARCHAR(255)  NOT NULL,
  `legende`   VARCHAR(300)  DEFAULT NULL,
  `ordre`     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_projet_ordre` (`projet_id`, `ordre`),
  CONSTRAINT `fk_photos_projet`
    FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Pages fixes (Équipe, Contact)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `id`      TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`    VARCHAR(80)      NOT NULL,
  `titre`   VARCHAR(100)     NOT NULL,
  `contenu` TEXT             DEFAULT NULL,
  `ordre`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (`slug`, `titre`, `ordre`) VALUES
  ('equipe',   'Équipe',   1),
  ('contact',  'Contact',  2);

-- ----------------------------
-- Utilisateurs admin
-- ----------------------------
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id`            TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login`         VARCHAR(80)      NOT NULL,
  `password_hash` VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
