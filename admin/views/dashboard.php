<?php
$nb_projets = DB::fetchOne("SELECT COUNT(*) AS n FROM projets")['n'];
$nb_publies = DB::fetchOne("SELECT COUNT(*) AS n FROM projets WHERE statut = 'publié'")['n'];
$nb_photos  = DB::fetchOne("SELECT COUNT(*) AS n FROM photos")['n'];
$nb_pages   = DB::fetchOne("SELECT COUNT(*) AS n FROM pages")['n'];
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

<p><a href="<?= APP_URL ?>/" target="_blank" class="btn">Voir le site &rarr;</a></p>
