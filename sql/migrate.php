<?php
// migrate.php — Script de migration Indexhibit → nbarch
// À exécuter UNE SEULE FOIS via : php migrate.php
// Supprimer ensuite du serveur.

$old = new PDO('mysql:host=gfeu.myd.infomaniak.com;dbname=gfeu_nbarch;charset=utf8mb4',
    'gfeu_nbarch', 'BovardNabarch1313!',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$new = new PDO('mysql:host=gfeu.myd.infomaniak.com;dbname=gfeu_26nbarch;charset=utf8mb4',
    'gfeu_26nbarch', 'BovardNabarch1313!',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$map = [4 => 1, 9 => 2, 10 => 3, 6 => 4];

// --- 1. Projets ---
echo "Migration projets...\n";
$rows = $old->query("
    SELECT id, section_id, title, url, content, year, ord
    FROM ndxz_objects
    WHERE section_id IN (4,6,9,10) AND hidden=0 AND status=1
    ORDER BY section_id, ord
")->fetchAll(PDO::FETCH_ASSOC);

$ins = $new->prepare("
    INSERT INTO projets (id, categorie_id, titre, slug, description, annee, statut, ordre)
    VALUES (?, ?, ?, ?, ?, ?, 'publié', ?)
");

foreach ($rows as $r) {
    $slug = basename(rtrim($r['url'], '/'));
    $desc = trim($r['content']) ?: null;
    $annee = $r['year'] ?: null;
    $ins->execute([$r['id'], $map[$r['section_id']], $r['title'], $slug, $desc, $annee, $r['ord']]);
}
echo count($rows) . " projets migrés.\n";

// --- 2. Photos ---
echo "Migration photos...\n";
$medias = $old->query("
    SELECT media_ref_id, media_file, media_caption, media_order
    FROM ndxz_media
    WHERE media_hide = 0
    ORDER BY media_ref_id, media_order
")->fetchAll(PDO::FETCH_ASSOC);

// IDs projets migrés
$ids = array_column($rows, 'id');

$insP = $new->prepare("
    INSERT INTO photos (projet_id, filename, legende, ordre)
    VALUES (?, ?, ?, ?)
");

$nb = 0;
foreach ($medias as $m) {
    if (!in_array($m['media_ref_id'], $ids)) continue;
    $legende = trim($m['media_caption']) ?: null;
    $insP->execute([$m['media_ref_id'], $m['media_file'], $legende, $m['media_order']]);
    $nb++;
}
echo "$nb photos migrées.\n";

// --- 3. Pages fixes ---
echo "Migration pages...\n";
$pages = $old->query("
    SELECT url, content FROM ndxz_objects WHERE section_id=1 AND status=1
")->fetchAll(PDO::FETCH_ASSOC);

$updP = $new->prepare("UPDATE pages SET contenu=? WHERE slug=?");
foreach ($pages as $p) {
    $slug = basename(rtrim($p['url'], '/'));
    $contenu = trim($p['content']) ?: null;
    $updP->execute([$contenu, $slug]);
}
echo count($pages) . " pages migrées.\n";

echo "\n=== Migration terminée ===\n";
