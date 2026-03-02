<?php
// Gestion photos d'un projet
$id = (int)($_GET['id'] ?? 0);
$projet = DB::fetchOne('SELECT * FROM projets WHERE id = ?', [$id]);
if (!$projet) {
    flash('Projet introuvable.', 'err');
    redirect(APP_URL . '/nbadmin/projets');
}
$photos = DB::fetchAll('SELECT * FROM photos WHERE projet_id = ? ORDER BY ordre', [$id]);
$cover = $photos[0] ?? null;
?>

<p style="margin-bottom:16px">
    <a href="<?= APP_URL ?>/nbadmin/projets/edit?id=<?= $id ?>" class="btn btn-sm">&larr; Retour au projet</a>
    <strong style="margin-left:12px"><?= e($projet['titre']) ?></strong>
    <span style="color:#888">(<?= count($photos) ?> photo<?= count($photos) > 1 ? 's' : '' ?>)</span>
</p>

<!-- Upload -->
<form method="post" action="<?= APP_URL ?>/actions/photo_upload.php" enctype="multipart/form-data" style="margin-bottom:24px">
    <?= csrf_field() ?>
    <input type="hidden" name="projet_id" value="<?= $id ?>">
    <div style="display:flex; gap:8px; align-items:center">
        <input type="file" name="photos[]" multiple accept="image/*" required>
        <button type="submit" class="btn btn-primary">Uploader</button>
    </div>
    <p style="font-size:11px;color:#888;margin-top:4px">
        JPEG, PNG, GIF, WebP — max <?= MAX_UPLOAD_SIZE / 1024 / 1024 ?> Mo par fichier
    </p>
</form>

<?php if (empty($photos)): ?>
<p style="color:#888">Aucune photo. Uploadez des images ci-dessus.</p>
<?php else: ?>

<!-- Grille photos (drag-and-drop) -->
<div class="photo-grid" id="photoGrid" data-projet="<?= $id ?>">
    <?php foreach ($photos as $i => $photo): ?>
    <div class="photo-card<?= $i === 0 ? ' is-cover' : '' ?>" data-id="<?= $photo['id'] ?>">
        <img src="<?= photo_url($id, $photo['filename']) ?>" alt="">
        <?php if ($i === 0): ?>
        <span class="photo-badge">COUVERTURE</span>
        <?php endif; ?>
        <div class="photo-actions">
            <?php if ($i !== 0): ?>
            <button type="button" class="btn btn-sm btn-cover" data-id="<?= $photo['id'] ?>">Couverture</button>
            <?php endif; ?>
            <form method="post" action="<?= APP_URL ?>/actions/photo_delete.php" style="display:inline"
                  onsubmit="return confirm('Supprimer cette photo ?')">
                <?= csrf_field() ?>
                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                <input type="hidden" name="projet_id" value="<?= $id ?>">
                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>
