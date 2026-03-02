<?php
$id = (int)($_GET['id'] ?? 0);
$page = DB::fetchOne('SELECT * FROM pages WHERE id = ?', [$id]);
if (!$page) {
    flash('Page introuvable.', 'err');
    redirect(APP_URL . '/nbadmin/pages');
}
?>

<form method="post" action="<?= APP_URL ?>/actions/page_save.php">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $page['id'] ?>">

    <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" value="<?= e($page['titre']) ?>" required>
    </div>

    <div class="form-group">
        <label for="contenu">Contenu (HTML)</label>
        <textarea id="contenu" name="contenu"><?= e($page['contenu'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= APP_URL ?>/nbadmin/pages" class="btn">Annuler</a>
    </div>
</form>
