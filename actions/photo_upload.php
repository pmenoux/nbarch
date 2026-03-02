<?php
// POST — Upload de photos multiples

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../nbadmin/auth.php';

require_login();
csrf_check();

$projet_id = (int)($_POST['projet_id'] ?? 0);

$projet = DB::fetchOne('SELECT id FROM projets WHERE id = ?', [$projet_id]);
if (!$projet) {
    flash('Projet introuvable.', 'err');
    redirect(APP_URL . '/nbadmin/projets');
}

// Dossier destination
$dir = UPLOAD_DIR . $projet_id;
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Ordre max actuel
$max_ordre = DB::fetchOne(
    'SELECT COALESCE(MAX(ordre), -1) AS m FROM photos WHERE projet_id = ?',
    [$projet_id]
)['m'];

$count = 0;
$files = $_FILES['photos'] ?? [];

// Réorganiser le tableau $_FILES pour itérer par fichier
$nb = is_array($files['name']) ? count($files['name']) : 0;

for ($i = 0; $i < $nb; $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
    if ($files['size'][$i] > MAX_UPLOAD_SIZE) continue;

    $original = $files['name'][$i];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT)) continue;

    // Nom sécurisé : garder le nom original nettoyé
    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($original, PATHINFO_FILENAME));
    $filename = $safe . '.' . $ext;

    // Éviter les doublons de nom
    $target = $dir . '/' . $filename;
    $n = 1;
    while (file_exists($target)) {
        $filename = $safe . '_' . $n . '.' . $ext;
        $target = $dir . '/' . $filename;
        $n++;
    }

    if (move_uploaded_file($files['tmp_name'][$i], $target)) {
        $max_ordre++;
        DB::query(
            'INSERT INTO photos (projet_id, filename, ordre) VALUES (?, ?, ?)',
            [$projet_id, $filename, $max_ordre]
        );
        $count++;
    }
}

if ($count > 0) {
    flash("$count photo" . ($count > 1 ? 's' : '') . " uploadée" . ($count > 1 ? 's' : '') . ".");
} else {
    flash('Aucune photo uploadée. Vérifiez le format et la taille.', 'err');
}

redirect(APP_URL . '/nbadmin/projets/photos?id=' . $projet_id);
