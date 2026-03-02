<?php
// POST — Créer, modifier ou supprimer une page

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
    DB::query('DELETE FROM pages WHERE id = ?', [$id]);
    flash('Page supprimée.');
    redirect(APP_URL . '/nbadmin/pages');
}

// --- Validation ---
$titre   = trim($_POST['titre'] ?? '');
$contenu = trim($_POST['contenu'] ?? '');

if ($titre === '') {
    flash('Le titre est requis.', 'err');
    redirect(APP_URL . '/nbadmin/pages/' . ($id ? "edit?id=$id" : 'nouveau'));
}

if ($id > 0) {
    // --- Modification ---
    DB::query(
        'UPDATE pages SET titre = ?, contenu = ? WHERE id = ?',
        [$titre, $contenu ?: null, $id]
    );
    flash('Page mise à jour.');
    redirect(APP_URL . '/nbadmin/pages/edit?id=' . $id);
} else {
    // --- Création ---
    $slug = make_slug($titre);

    // Vérifier unicité du slug parmi les pages
    $existing = DB::fetchOne('SELECT id FROM pages WHERE slug = ?', [$slug]);
    if ($existing) {
        $slug .= '-' . time();
    }

    // Vérifier que le slug ne conflit pas avec une catégorie
    $cat_conflict = DB::fetchOne('SELECT id FROM categories WHERE slug = ?', [$slug]);
    if ($cat_conflict) {
        $slug = 'page-' . $slug;
    }

    $max_ordre = DB::fetchOne('SELECT COALESCE(MAX(ordre), 0) AS m FROM pages')['m'];

    DB::query(
        'INSERT INTO pages (titre, slug, contenu, ordre) VALUES (?, ?, ?, ?)',
        [$titre, $slug, $contenu ?: null, $max_ordre + 1]
    );
    $new_id = DB::lastInsertId();
    flash('Page créée.');
    redirect(APP_URL . '/nbadmin/pages/edit?id=' . $new_id);
}
