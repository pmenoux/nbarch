<?php
// Formulaire création / édition page
$id = (int)($_GET['id'] ?? 0);
$page = null;
$is_edit = false;

if ($id > 0) {
    $page = DB::fetchOne('SELECT * FROM pages WHERE id = ?', [$id]);
    if (!$page) {
        flash('Page introuvable.', 'err');
        redirect(APP_URL . '/nbadmin/pages');
    }
    $is_edit = true;
}
?>

<form method="post" action="<?= APP_URL ?>/actions/page_save.php">
    <?= csrf_field() ?>
    <?php if ($is_edit): ?>
    <input type="hidden" name="id" value="<?= $page['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre"
               value="<?= e($page['titre'] ?? '') ?>" required>
    </div>

    <?php if ($is_edit): ?>
    <div class="form-group">
        <label>URL</label>
        <div style="color:#888; font-size:.9rem">/<?= e($page['slug']) ?></div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label>Contenu</label>
        <div id="editor-contenu"><?= $page['contenu'] ?? '' ?></div>
        <input type="hidden" id="contenu" name="contenu">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $is_edit ? 'Enregistrer' : 'Créer la page' ?>
        </button>
        <a href="<?= APP_URL ?>/nbadmin/pages" class="btn">Annuler</a>
        <?php if ($is_edit): ?>
        <button type="submit" name="delete" value="1" class="btn btn-danger"
                onclick="return confirm('Supprimer cette page ?')">
            Supprimer
        </button>
        <?php endif; ?>
    </div>
</form>
