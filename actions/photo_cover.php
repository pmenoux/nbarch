<?php
// AJAX POST — Définir une photo comme couverture (ordre = 0)

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../nbadmin/auth.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non connecté']);
    exit;
}

csrf_check();

$photo_id  = (int)($_POST['photo_id'] ?? 0);
$projet_id = (int)($_POST['projet_id'] ?? 0);

$photo = DB::fetchOne('SELECT * FROM photos WHERE id = ? AND projet_id = ?', [$photo_id, $projet_id]);
if (!$photo) {
    http_response_code(404);
    echo json_encode(['error' => 'Photo introuvable']);
    exit;
}

// Renuméroter : la photo sélectionnée passe en ordre 0, les autres suivent
$all = DB::fetchAll('SELECT id FROM photos WHERE projet_id = ? ORDER BY ordre', [$projet_id]);
$ordre = 0;
// D'abord la couverture
DB::query('UPDATE photos SET ordre = 0 WHERE id = ?', [$photo_id]);
$ordre = 1;
foreach ($all as $p) {
    if ($p['id'] == $photo_id) continue;
    DB::query('UPDATE photos SET ordre = ? WHERE id = ?', [$ordre, $p['id']]);
    $ordre++;
}

echo json_encode(['ok' => true, 'cover_id' => $photo_id]);
