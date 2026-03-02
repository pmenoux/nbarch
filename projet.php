<?php
// projet.php — Page projet individuel
// URL : /realisations/rosiere → ?cat=realisations&slug=rosiere

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$cat_slug    = $_GET['cat']  ?? '';
$projet_slug = $_GET['slug'] ?? '';

// Récupérer la catégorie
$categorie = DB::fetchOne('SELECT * FROM categories WHERE slug = ?', [$cat_slug]);

// Récupérer le projet
$projet = null;
$photos = [];
if ($categorie) {
    $projet = DB::fetchOne(
        'SELECT * FROM projets WHERE slug = ? AND categorie_id = ? AND statut = ?',
        [$projet_slug, $categorie['id'], 'publié']
    );
    if ($projet) {
        $photos = DB::fetchAll(
            'SELECT * FROM photos WHERE projet_id = ? ORDER BY ordre',
            [$projet['id']]
        );
    }
}

// Contexte sidebar + titre
$current_cat_slug    = $cat_slug;
$current_projet_slug = $projet_slug;
$page_title          = $projet
    ? $projet['titre'] . ' — ' . APP_NAME
    : 'Introuvable — ' . APP_NAME;

require_once __DIR__ . '/includes/header.php';

if (!$categorie || !$projet):
    http_response_code(404);
?>
    <p>Projet introuvable.</p>
<?php else: ?>

<h1 class="projet-titre"><?= e(mb_strtoupper($projet['titre'])) ?></h1>

<?php
// Nettoyer la description : supprimer le premier <p><strong>...</strong></p>
// (toujours un doublon du titre dans les données Indexhibit)
$description = $projet['description'] ?? '';
$description = preg_replace('#<p>\s*<strong>[^<]*</strong>\s*</p>#iu', '', $description, 1);
$description = trim($description);
?>
<?php if ($description): ?>
<div class="projet-meta">
    <?= $description ?>
</div>
<?php endif; ?>

<?php if (!empty($photos)): ?>
<div class="projet-credits">
    photographies – tous droits réservés
</div>

<div class="projet-galerie">
    <?php foreach ($photos as $photo): ?>
    <img src="<?= photo_url($projet['id'], $photo['filename']) ?>"
         alt="<?= e($projet['titre']) ?>"
         loading="lazy">
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="copyright">Tous droits réservés</div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
