<?php
// index.php — Page d'accueil
// Affiche la couverture du premier projet de la première catégorie

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

// Premier projet publié (catégorie réalisations par défaut)
$premier = DB::fetchOne(
    "SELECT p.*, c.slug AS cat_slug
     FROM projets p
     JOIN categories c ON c.id = p.categorie_id
     WHERE p.statut = 'publié'
     ORDER BY c.ordre, p.ordre
     LIMIT 1"
);

if (!$premier) {
    $page_title = APP_NAME;
    $current_cat_slug = '';
    require_once __DIR__ . '/includes/header.php';
    echo '<p>Aucun projet publié.</p>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Contexte sidebar
$current_cat_slug    = $premier['cat_slug'];
$current_projet_slug = '';
$page_title          = APP_NAME;

require_once __DIR__ . '/includes/header.php';

// Photo de couverture du premier projet
$cover = get_cover($premier['id']);
?>

<?php if ($cover): ?>
<div class="projet-galerie">
    <a href="<?= APP_URL ?>/<?= e($premier['cat_slug']) ?>/<?= e($premier['slug']) ?>">
        <img src="<?= photo_url($premier['id'], $cover['filename']) ?>"
             alt="<?= e($premier['titre']) ?>">
    </a>
</div>
<?php endif; ?>

<div class="copyright">Tous droits réservés</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
