<?php
// POST — Créer, modifier ou supprimer une catégorie

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../nbadmin/auth.php';

require_login();
csrf_check();

$id = (int)($_POST['id'] ?? 0);

// --- Suppression ---
if (!empty($_POST['delete']) && $id > 0) {
    // Supprimer les photos des projets de cette catégorie
    $projets = DB::fetchAll('SELECT id FROM projets WHERE categorie_id = ?', [$id]);
    foreach ($projets as $p) {
        $dir = UPLOAD_DIR . $p['id'];
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $f) {
                if (is_file($f)) unlink($f);
            }
            rmdir($dir);
        }
    }
    // CASCADE supprime les projets et photos en DB
    DB::query('DELETE FROM projets WHERE categorie_id = ?', [$id]);
    DB::query('DELETE FROM categories WHERE id = ?', [$id]);
    flash('Catégorie supprimée.');
    redirect(APP_URL . '/nbadmin/categories');
}

// --- Validation ---
$nom = trim($_POST['nom'] ?? '');

if ($nom === '') {
    flash('Le nom est requis.', 'err');
    redirect(APP_URL . '/nbadmin/categories/' . ($id ? "edit?id=$id" : 'nouveau'));
}

$slug = make_slug($nom);

// Vérifier unicité du slug
$existing = DB::fetchOne(
    'SELECT id FROM categories WHERE slug = ? AND id != ?',
    [$slug, $id]
);
if ($existing) {
    $slug .= '-' . time();
}

if ($id > 0) {
    // --- Modification ---
    DB::query(
        'UPDATE categories SET nom = ?, slug = ? WHERE id = ?',
        [$nom, $slug, $id]
    );
    flash('Catégorie mise à jour.');
    redirect(APP_URL . '/nbadmin/categories/edit?id=' . $id);
} else {
    // --- Création ---
    $max_ordre = DB::fetchOne('SELECT COALESCE(MAX(ordre), 0) AS m FROM categories')['m'];

    DB::query(
        'INSERT INTO categories (nom, slug, ordre) VALUES (?, ?, ?)',
        [$nom, $slug, $max_ordre + 1]
    );
    flash('Catégorie créée.');
    redirect(APP_URL . '/nbadmin/categories');
}
