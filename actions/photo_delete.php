<?php
// POST — Supprimer une photo

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../admin/auth.php';

require_login();
csrf_check();

$photo_id   = (int)($_POST['photo_id'] ?? 0);
$projet_id  = (int)($_POST['projet_id'] ?? 0);

$photo = DB::fetchOne('SELECT * FROM photos WHERE id = ? AND projet_id = ?', [$photo_id, $projet_id]);

if ($photo) {
    // Supprimer le fichier
    $file = UPLOAD_DIR . $projet_id . '/' . $photo['filename'];
    if (is_file($file)) {
        unlink($file);
    }
    DB::query('DELETE FROM photos WHERE id = ?', [$photo_id]);
    flash('Photo supprimée.');
} else {
    flash('Photo introuvable.', 'err');
}

redirect(APP_URL . '/admin/projets/photos?id=' . $projet_id);
