<?php
// Layout admin (header, nav, flash, contenu, footer)
$admin = current_admin();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_admin_title) ?> — Admin <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/nbadmin/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
</head>
<body>

<header class="admin-header">
    <a href="<?= APP_URL ?>/nbadmin/" class="admin-logo">NB.ARCH</a>
    <nav class="admin-nav">
        <a href="<?= APP_URL ?>/nbadmin/projets"<?= str_starts_with($route, 'projets') ? ' class="active"' : '' ?>>Projets</a>
        <a href="<?= APP_URL ?>/nbadmin/categories"<?= str_starts_with($route, 'categories') ? ' class="active"' : '' ?>>Catégories</a>
        <a href="<?= APP_URL ?>/nbadmin/pages"<?= str_starts_with($route, 'pages') ? ' class="active"' : '' ?>>Pages</a>
        <a href="<?= APP_URL ?>/nbadmin/aide"<?= $route === 'aide' ? ' class="active"' : '' ?>>Aide</a>
    </nav>
    <div class="admin-user">
        <span><?= e($admin['login'] ?? '') ?></span>
        <form method="post" action="<?= APP_URL ?>/actions/logout.php" style="display:inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn-link">Déconnexion</button>
        </form>
    </div>
</header>

<main class="admin-main">
    <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
    <?php endif; ?>

    <h1><?= e($page_admin_title) ?></h1>

    <?php if ($content_file && file_exists($content_file)): ?>
        <?php require $content_file; ?>
    <?php else: ?>
        <p>Page introuvable.</p>
    <?php endif; ?>
</main>

<footer class="admin-footer">v<?= APP_VERSION ?></footer>

<script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="<?= APP_URL ?>/nbadmin/js/admin.js"></script>
</body>
</html>
