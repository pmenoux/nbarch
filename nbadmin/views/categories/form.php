<?php
// Formulaire création / édition catégorie
$id = (int)($_GET['id'] ?? 0);
$categorie = null;
$is_edit = false;

if ($id > 0) {
    $categorie = DB::fetchOne('SELECT * FROM categories WHERE id = ?', [$id]);
    if (!$categorie) {
        flash('Catégorie introuvable.', 'err');
        redirect(APP_URL . '/nbadmin/categories');
    }
    $is_edit = true;
}

// Nombre de projets dans cette catégorie (pour avertissement suppression)
$nb_projets = $is_edit
    ? DB::fetchOne('SELECT COUNT(*) AS n FROM projets WHERE categorie_id = ?', [$id])['n']
    : 0;
?>

<form method="post" action="<?= APP_URL ?>/actions/categorie_save.php">
    <?= csrf_field() ?>
    <?php if ($is_edit): ?>
    <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom"
               value="<?= e($categorie['nom'] ?? '') ?>" required>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $is_edit ? 'Enregistrer' : 'Créer la catégorie' ?>
        </button>
        <a href="<?= APP_URL ?>/nbadmin/categories" class="btn">Annuler</a>
        <?php if ($is_edit): ?>
        <button type="submit" name="delete" value="1" class="btn btn-danger"
                onclick="return confirm('Supprimer cette catégorie<?= $nb_projets > 0 ? ' et ses ' . $nb_projets . ' projet(s)' : '' ?> ?')">
            Supprimer
        </button>
        <?php endif; ?>
    </div>
</form>
