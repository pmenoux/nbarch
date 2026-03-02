<?php
// categorie.php — Clic sur une catégorie
// URL : /realisations → ?slug=realisations
// Comportement Indexhibit : redirige vers le premier projet de la catégorie

$cat_slug = $_GET['slug'] ?? '';

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

// Récupérer la catégorie
$categorie = DB::fetchOne('SELECT * FROM categories WHERE slug = ?', [$cat_slug]);

if (!$categorie) {
    // Peut-être une page fixe ?
    $page = DB::fetchOne('SELECT * FROM pages WHERE slug = ?', [$cat_slug]);
    if ($page) {
        // Affiche la page fixe
        $current_cat_slug    = '';
        $current_projet_slug = '';
        $current_page_slug   = $cat_slug;
        $page_title          = e($page['titre']) . ' — ' . APP_NAME;

        require_once __DIR__ . '/includes/header.php';
        ?>
        <h1 class="page-titre"><?= e(mb_strtoupper($page['titre'])) ?></h1>
        <div class="page-contenu">
            <?= $page['contenu'] ?>
        </div>
        <?php
        require_once __DIR__ . '/includes/footer.php';
        exit;
    }

    http_response_code(404);
    $current_cat_slug = '';
    $page_title = 'Page introuvable — ' . APP_NAME;
    require_once __DIR__ . '/includes/header.php';
    echo '<p>Page introuvable.</p>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Premier projet publié de la catégorie
$premier = DB::fetchOne(
    "SELECT slug FROM projets WHERE categorie_id = ? AND statut = 'publié' ORDER BY ordre LIMIT 1",
    [$categorie['id']]
);

if ($premier) {
    redirect(APP_URL . '/' . $cat_slug . '/' . $premier['slug']);
} else {
    // Catégorie vide
    $current_cat_slug = $cat_slug;
    $page_title = e($categorie['nom']) . ' — ' . APP_NAME;
    require_once __DIR__ . '/includes/header.php';
    echo '<p>Aucun projet dans cette catégorie.</p>';
    require_once __DIR__ . '/includes/footer.php';
}
