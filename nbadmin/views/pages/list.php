<?php
$pages = DB::fetchAll('SELECT * FROM pages ORDER BY ordre');
?>

<table class="table">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Slug</th>
            <th class="actions">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pages as $p): ?>
        <tr>
            <td style="font-weight:500"><?= e($p['titre']) ?></td>
            <td style="color:#888">/<?= e($p['slug']) ?></td>
            <td class="actions">
                <a href="<?= APP_URL ?>/nbadmin/pages/edit?id=<?= $p['id'] ?>" class="btn btn-sm">Modifier</a>
                <a href="<?= APP_URL ?>/<?= e($p['slug']) ?>" target="_blank" class="btn btn-sm">Voir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
