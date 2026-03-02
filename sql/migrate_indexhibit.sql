-- Migration Indexhibit → nbarch custom
-- v0.1 — 2026-03-02
--
-- Prérequis :
--   1. schema.sql déjà exécuté sur la NOUVELLE base (gfeu_26nbarch)
--   2. L'utilisateur MySQL doit avoir accès aux deux bases
--
-- Remplacer gfeu_26nbarch par le nom réel de la nouvelle base avant d'exécuter.
-- Exécuter dans phpMyAdmin (connecté à l'ancienne base gfeu_nbarch)
-- ou dans Navicat avec : USE gfeu_nbarch;

SET NAMES utf8mb4;

-- ----------------------------
-- 1. Projets (77 projets depuis sections 4, 6, 9, 10)
-- ----------------------------
INSERT INTO gfeu_26nbarch.projets (id, categorie_id, titre, slug, description, annee, statut, ordre)
SELECT
  o.id,
  CASE o.section_id
    WHEN 4  THEN 1   -- realisations
    WHEN 9  THEN 2   -- scenographies
    WHEN 10 THEN 3   -- boutiques
    WHEN 6  THEN 4   -- concours
  END                                                   AS categorie_id,
  o.title                                               AS titre,
  SUBSTRING_INDEX(TRIM(TRAILING '/' FROM o.url), '/', -1) AS slug,
  NULLIF(TRIM(o.content), '')                           AS description,
  NULLIF(o.year, '')                                    AS annee,
  'publié'                                              AS statut,
  o.ord                                                 AS ordre
FROM ndxz_objects o
WHERE o.section_id IN (4, 6, 9, 10)
  AND o.hidden  = 0
  AND o.status  = 1
ORDER BY o.section_id, o.ord;

-- Vérification : doit retourner ~77
-- SELECT COUNT(*) FROM gfeu_26nbarch.projets;

-- ----------------------------
-- 2. Photos
-- ----------------------------
INSERT INTO gfeu_26nbarch.photos (projet_id, filename, legende, ordre)
SELECT
  m.media_ref_id,
  m.media_file,
  NULLIF(TRIM(m.media_caption), ''),
  m.media_order
FROM ndxz_media m
WHERE m.media_ref_id IN (SELECT id FROM gfeu_26nbarch.projets)
  AND m.media_hide = 0
ORDER BY m.media_ref_id, m.media_order;

-- Vérification : nombre de photos migrées
-- SELECT COUNT(*) FROM gfeu_26nbarch.photos;

-- ----------------------------
-- 3. Pages fixes (section 1 = informations → equipe + contact)
-- ----------------------------
UPDATE gfeu_26nbarch.pages p
JOIN ndxz_objects o
  ON SUBSTRING_INDEX(TRIM(TRAILING '/' FROM o.url), '/', -1) = p.slug
  AND o.section_id = 1
SET p.contenu = NULLIF(TRIM(o.content), '');
