<?php
// Liste des projets par catégorie
$categories = DB::fetchAll('SELECT * FROM categories ORDER BY ordre');
$projets = DB::fetchAll(
    "SELECT p.*, c.nom AS cat_nom, c.slug AS cat_slug,
            (SELECT COUNT(*) FROM photos WHERE projet_id = p.id) AS nb_photos
     FROM projets p
     JOIN categories c ON c.id = p.categorie_id
     ORDER BY c.ordre, p.ordre"
);
// Grouper par catégorie
$par_cat = [];
foreach ($projets as $p) {
    $par_cat[$p['cat_nom']][] = $p;
}
?>

<div class="toolbar">
    <div></div>
    <a href="<?= APP_URL ?>/admin/projets/nouveau" class="btn btn-primary">+ Nouveau projet</a>
</div>

<?php foreach ($par_cat as $cat_nom => $liste): ?>
<h2 style="font-size:14px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#888; margin:24px 0 8px;">
    <?= e($cat_nom) ?> (<?= count($liste) ?>)
</h2>
<table class="table">
    <thead>
        <tr>
            <th style="width:40px">#</th>
            <th>Titre</th>
            <th>Statut</th>
            <th>Photos</th>
            <th class="actions">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($liste as $p): ?>
        <tr>
            <td style="color:#aaa"><?= $p['ordre'] ?></td>
            <td>
                <a href="<?= APP_URL ?>/admin/projets/edit?id=<?= $p['id'] ?>" style="font-weight:500;color:#000;text-decoration:none">
                    <?= e($p['titre']) ?>
                </a>
            </td>
            <td>
                <?php if ($p['statut'] === 'publié'): ?>
                    <span class="badge badge-ok">Publié</span>
                <?php else: ?>
                    <span class="badge badge-draft">Brouillon</span>
                <?php endif; ?>
            </td>
            <td><?= $p['nb_photos'] ?></td>
            <td class="actions">
                <a href="<?= APP_URL ?>/admin/projets/photos?id=<?= $p['id'] ?>" class="btn btn-sm">Photos</a>
                <a href="<?= APP_URL ?>/admin/projets/edit?id=<?= $p['id'] ?>" class="btn btn-sm">Modifier</a>
                <a href="<?= APP_URL ?>/<?= e($p['cat_slug']) ?>/<?= e($p['slug']) ?>" target="_blank" class="btn btn-sm">Voir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endforeach; ?>
