<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Catégories + projets pour la navigation
$categories = DB::fetchAll('SELECT * FROM categories ORDER BY ordre');
$all_projets = DB::fetchAll(
    "SELECT id, categorie_id, titre, slug FROM projets WHERE statut = 'publié' ORDER BY ordre"
);
// Grouper les projets par catégorie
$projets_par_cat = [];
foreach ($all_projets as $p) {
    $projets_par_cat[$p['categorie_id']][] = $p;
}
// Pages fixes
$pages_fixes = DB::fetchAll('SELECT slug, titre FROM pages ORDER BY ordre');

// Variables de contexte (définies par la page appelante)
$current_cat_slug    = $current_cat_slug ?? '';
$current_projet_slug = $current_projet_slug ?? '';
$current_page_slug   = $current_page_slug ?? '';
$page_title          = $page_title ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="site">

    <nav class="sidebar">
        <a href="<?= APP_URL ?>/" class="logo">NB.ARCH</a>

        <div class="sidebar-nav">
            <?php foreach ($categories as $cat): ?>
            <?php
                $is_active   = ($cat['slug'] === $current_cat_slug);
                $cat_projets = $projets_par_cat[$cat['id']] ?? [];
            ?>
            <div class="nav-cat<?= $is_active ? ' active' : '' ?>">
                <a href="<?= APP_URL ?>/<?= e($cat['slug']) ?>" class="nav-cat-title">
                    <?= e(mb_strtoupper($cat['nom'])) ?>
                    <span class="arrow">&#9660;</span>
                </a>
                <?php if (!empty($cat_projets)): ?>
                <ul class="nav-projets">
                    <?php foreach ($cat_projets as $p): ?>
                    <li>
                        <a href="<?= APP_URL ?>/<?= e($cat['slug']) ?>/<?= e($p['slug']) ?>"
                           <?= ($p['slug'] === $current_projet_slug && $is_active) ? 'class="current"' : '' ?>>
                            <?= e(mb_strtoupper($p['titre'])) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <div class="nav-pages">
                <?php foreach ($pages_fixes as $pf): ?>
                <a href="<?= APP_URL ?>/<?= e($pf['slug']) ?>"
                   class="nav-page<?= ($pf['slug'] === $current_page_slug) ? ' current' : '' ?>">
                    <?= e(mb_strtoupper($pf['titre'])) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sidebar-footer">
            &copy; NB.ARCH<br>
            Tous droits réservés
        </div>
    </nav>

    <main class="content">
