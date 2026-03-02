<?php
$nb_projets = DB::fetchOne("SELECT COUNT(*) AS n FROM projets")['n'];
$nb_publies = DB::fetchOne("SELECT COUNT(*) AS n FROM projets WHERE statut = 'publié'")['n'];
$nb_photos  = DB::fetchOne("SELECT COUNT(*) AS n FROM photos")['n'];
$nb_pages   = DB::fetchOne("SELECT COUNT(*) AS n FROM pages")['n'];

// Projets publiés pour le sélecteur d'accueil
$projets_publies = DB::fetchAll(
    "SELECT p.id, p.titre, c.nom AS cat_nom
     FROM projets p
     JOIN categories c ON c.id = p.categorie_id
     WHERE p.statut = 'publié'
     ORDER BY c.ordre, p.ordre"
);

// Réglage actuel
$accueil_id = get_reglage('accueil_projet_id');
?>

<div class="stats">
    <div class="stat-card">
        <div class="number"><?= $nb_projets ?></div>
        <div class="label">Projets</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $nb_publies ?></div>
        <div class="label">Publiés</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $nb_photos ?></div>
        <div class="label">Photos</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $nb_pages ?></div>
        <div class="label">Pages</div>
    </div>
</div>

<!-- Sélection image d'accueil -->
<div class="accueil-section">
    <h2>Image d'accueil</h2>
    <p class="accueil-help">Sélectionnez le projet dont la couverture sera affichée sur la page d'accueil.</p>

    <form method="post" action="<?= APP_URL ?>/actions/reglage_save.php">
        <?= csrf_field() ?>

        <div class="accueil-grid">
            <!-- Option auto (premier projet) -->
            <label class="accueil-card<?= !$accueil_id ? ' selected' : '' ?>">
                <input type="radio" name="accueil_projet_id" value="0"
                       <?= !$accueil_id ? 'checked' : '' ?>>
                <div class="accueil-thumb accueil-auto">AUTO</div>
                <div class="accueil-name">Premier projet</div>
            </label>

            <?php foreach ($projets_publies as $pp):
                $cover = get_cover($pp['id']);
                $is_selected = ((int)$accueil_id === $pp['id']);
            ?>
            <label class="accueil-card<?= $is_selected ? ' selected' : '' ?>">
                <input type="radio" name="accueil_projet_id" value="<?= $pp['id'] ?>"
                       <?= $is_selected ? 'checked' : '' ?>>
                <?php if ($cover): ?>
                <img class="accueil-thumb" src="<?= photo_url($pp['id'], $cover['filename']) ?>"
                     alt="<?= e($pp['titre']) ?>">
                <?php else: ?>
                <div class="accueil-thumb accueil-auto">—</div>
                <?php endif; ?>
                <div class="accueil-name"><?= e($pp['titre']) ?></div>
                <div class="accueil-cat"><?= e($pp['cat_nom']) ?></div>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>

<p style="margin-top: 24px;"><a href="<?= APP_URL ?>/" target="_blank" class="btn">Voir le site &rarr;</a></p>
