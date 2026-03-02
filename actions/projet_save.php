<?php
// POST — Créer, modifier ou supprimer un projet

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../admin/auth.php';

require_login();
csrf_check();

$id = (int)($_POST['id'] ?? 0);

// --- Suppression ---
if (!empty($_POST['delete']) && $id > 0) {
    // Supprimer le dossier photos
    $dir = UPLOAD_DIR . $id;
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $f) {
            if (is_file($f)) unlink($f);
        }
        rmdir($dir);
    }
    // CASCADE supprime les photos en DB
    DB::query('DELETE FROM projets WHERE id = ?', [$id]);
    flash('Projet supprimé.');
    redirect(APP_URL . '/admin/projets');
}

// --- Validation ---
$titre        = trim($_POST['titre'] ?? '');
$slug         = trim($_POST['slug'] ?? '');
$categorie_id = (int)($_POST['categorie_id'] ?? 0);
$description  = trim($_POST['description'] ?? '');
$credits      = trim($_POST['credits'] ?? '');
$statut       = in_array($_POST['statut'] ?? '', ['publié', 'brouillon']) ? $_POST['statut'] : 'publié';

if ($titre === '' || $categorie_id === 0) {
    flash('Titre et catégorie requis.', 'err');
    redirect(APP_URL . '/admin/projets/' . ($id ? "edit?id=$id" : 'nouveau'));
}

// Slug auto si vide
if ($slug === '') {
    $slug = make_slug($titre);
}

// Vérifier unicité du slug dans la catégorie
$existing = DB::fetchOne(
    'SELECT id FROM projets WHERE slug = ? AND categorie_id = ? AND id != ?',
    [$slug, $categorie_id, $id]
);
if ($existing) {
    $slug .= '-' . time();
}

if ($id > 0) {
    // --- Modification ---
    DB::query(
        'UPDATE projets SET titre = ?, slug = ?, categorie_id = ?, description = ?, credits = ?, statut = ? WHERE id = ?',
        [$titre, $slug, $categorie_id, $description ?: null, $credits ?: null, $statut, $id]
    );
    flash('Projet mis à jour.');
    redirect(APP_URL . '/admin/projets/edit?id=' . $id);
} else {
    // --- Création ---
    // Ordre : mettre à la fin
    $max_ordre = DB::fetchOne(
        'SELECT COALESCE(MAX(ordre), 0) AS m FROM projets WHERE categorie_id = ?',
        [$categorie_id]
    )['m'];

    DB::query(
        'INSERT INTO projets (titre, slug, categorie_id, description, credits, statut, ordre) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$titre, $slug, $categorie_id, $description ?: null, $credits ?: null, $statut, $max_ordre + 1]
    );
    $new_id = DB::lastInsertId();
    flash('Projet créé.');
    redirect(APP_URL . '/admin/projets/edit?id=' . $new_id);
}
