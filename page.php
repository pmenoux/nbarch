<?php
// page.php — Pages fixes (équipe, contact)
// URL : /equipe → ?slug=equipe

$slug = $_GET['slug'] ?? '';

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$page = DB::fetchOne('SELECT * FROM pages WHERE slug = ?', [$slug]);

if (!$page) {
    http_response_code(404);
    $current_cat_slug = '';
    $page_title = 'Page introuvable — ' . APP_NAME;
    require_once __DIR__ . '/includes/header.php';
    echo '<p>Page introuvable.</p>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$current_cat_slug    = '';
$current_projet_slug = '';
$page_title          = e($page['titre']) . ' — ' . APP_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<h1 class="page-titre"><?= e(mb_strtoupper($page['titre'])) ?></h1>

<div class="page-contenu">
    <?= $page['contenu'] ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
