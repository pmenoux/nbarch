<?php
$categories = DB::fetchAll('SELECT * FROM categories ORDER BY ordre');
?>

<p style="font-size:13px;color:#888;margin-bottom:16px">
    Glissez-déposez pour réordonner les catégories. L'ordre est sauvegardé automatiquement.
</p>

<ul class="sortable-list" data-url="<?= APP_URL ?>/actions/categories_reorder.php">
    <?php foreach ($categories as $cat): ?>
    <li class="sortable-item" data-id="<?= $cat['id'] ?>">
        <span class="handle">&#9776;</span>
        <span class="name"><?= e($cat['nom']) ?></span>
        <span style="color:#888;font-size:12px">/<?= e($cat['slug']) ?></span>
    </li>
    <?php endforeach; ?>
</ul>

<h2 style="font-size:16px;font-weight:600;margin:32px 0 12px">Ordre des projets par catégorie</h2>

<?php foreach ($categories as $cat):
    $projets = DB::fetchAll(
        "SELECT * FROM projets WHERE categorie_id = ? ORDER BY ordre",
        [$cat['id']]
    );
    if (empty($projets)) continue;
?>
<h3 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#888;margin:16px 0 8px">
    <?= e($cat['nom']) ?>
</h3>
<ul class="sortable-list" data-url="<?= APP_URL ?>/actions/projets_reorder.php">
    <?php foreach ($projets as $p): ?>
    <li class="sortable-item" data-id="<?= $p['id'] ?>">
        <span class="handle">&#9776;</span>
        <span class="name"><?= e($p['titre']) ?></span>
        <span style="font-size:12px" class="badge <?= $p['statut'] === 'publié' ? 'badge-ok' : 'badge-draft' ?>">
            <?= $p['statut'] === 'publié' ? 'Publié' : 'Brouillon' ?>
        </span>
    </li>
    <?php endforeach; ?>
</ul>
<?php endforeach; ?>
