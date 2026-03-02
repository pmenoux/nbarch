<?php
// Formulaire création / édition projet
$id = (int)($_GET['id'] ?? 0);
$projet = null;
$is_edit = false;

if ($id > 0) {
    $projet = DB::fetchOne('SELECT * FROM projets WHERE id = ?', [$id]);
    if (!$projet) {
        flash('Projet introuvable.', 'err');
        redirect(APP_URL . '/nbadmin/projets');
    }
    $is_edit = true;
}

$categories = DB::fetchAll('SELECT * FROM categories ORDER BY ordre');
?>

<form method="post" action="<?= APP_URL ?>/actions/projet_save.php">
    <?= csrf_field() ?>
    <?php if ($is_edit): ?>
    <input type="hidden" name="id" value="<?= $projet['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre"
               value="<?= e($projet['titre'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="slug">Slug (URL)</label>
        <input type="text" id="slug" name="slug"
               value="<?= e($projet['slug'] ?? '') ?>"
               placeholder="auto-généré depuis le titre">
    </div>

    <div class="form-group">
        <label for="categorie_id">Catégorie</label>
        <select id="categorie_id" name="categorie_id" required>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
                <?= (($projet['categorie_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                <?= e($cat['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Description</label>
        <div id="editor-description"><?= $projet['description'] ?? '' ?></div>
        <input type="hidden" id="description" name="description">
    </div>

    <div class="form-group">
        <label for="credits">Crédits photographiques</label>
        <input type="text" id="credits" name="credits"
               value="<?= e($projet['credits'] ?? '') ?>"
               placeholder="ex : Photographies — Nom du photographe">
    </div>

    <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
            <option value="publié" <?= (($projet['statut'] ?? 'publié') === 'publié') ? 'selected' : '' ?>>Publié</option>
            <option value="brouillon" <?= (($projet['statut'] ?? '') === 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $is_edit ? 'Enregistrer' : 'Créer le projet' ?>
        </button>
        <a href="<?= APP_URL ?>/nbadmin/projets" class="btn">Annuler</a>
        <?php if ($is_edit): ?>
        <button type="submit" name="delete" value="1" class="btn btn-danger"
                onclick="return confirm('Supprimer ce projet et toutes ses photos ?')">
            Supprimer
        </button>
        <?php endif; ?>
    </div>
</form>
