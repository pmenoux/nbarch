<?php

// Échappement HTML
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Génère un slug URL-safe depuis un titre
function make_slug(string $titre): string {
    $s = mb_strtolower($titre, 'UTF-8');
    $s = str_replace(
        ['à','â','ä','é','è','ê','ë','î','ï','ô','ö','ù','û','ü','ç','œ','æ'],
        ['a','a','a','e','e','e','e','i','i','o','o','u','u','u','c','oe','ae'],
        $s
    );
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

// Redirige et stoppe
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

// Flash messages (session)
function flash(string $msg, string $type = 'ok'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function get_flash(): ?array {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

// Couverture d'un projet : première photo (ordre le plus bas)
function get_cover(int $projet_id): array|false {
    return DB::fetchOne(
        'SELECT * FROM photos WHERE projet_id = ? ORDER BY ordre ASC LIMIT 1',
        [$projet_id]
    );
}

// URL d'une photo
function photo_url(int $projet_id, string $filename): string {
    return UPLOAD_URL . $projet_id . '/' . rawurlencode($filename);
}
