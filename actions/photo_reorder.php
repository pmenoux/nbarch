<?php
// AJAX POST — Réordonner les photos (drag-and-drop)

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../admin/auth.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non connecté']);
    exit;
}

csrf_check();

$ids = $_POST['ids'] ?? [];
if (!is_array($ids) || empty($ids)) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

foreach ($ids as $ordre => $id) {
    DB::query('UPDATE photos SET ordre = ? WHERE id = ?', [(int)$ordre, (int)$id]);
}

echo json_encode(['ok' => true]);
