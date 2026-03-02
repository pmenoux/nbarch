<?php
// Front controller admin — toutes les routes passent ici

define('APP_VERSION', '1.0.0');

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/auth.php';

$route = trim($_GET['route'] ?? '', '/');

// --- Page login (seule route publique) ---
if ($route === 'login') {
    require __DIR__ . '/login.php';
    exit;
}

// --- Toutes les autres routes nécessitent une session admin ---
require_login();

// --- Table de routage ---
$routes = [
    ''                 => 'views/dashboard.php',
    'projets'          => 'views/projets/list.php',
    'projets/nouveau'  => 'views/projets/form.php',
    'projets/edit'     => 'views/projets/form.php',
    'projets/photos'   => 'views/projets/photos.php',
    'categories'       => 'views/categories/list.php',
    'pages'            => 'views/pages/list.php',
    'pages/edit'       => 'views/pages/form.php',
];

if (!isset($routes[$route])) {
    http_response_code(404);
    $page_admin_title = '404';
    $content_file = null;
    require __DIR__ . '/layout.php';
    exit;
}

$page_admin_title = match ($route) {
    ''                 => 'Tableau de bord',
    'projets'          => 'Projets',
    'projets/nouveau'  => 'Nouveau projet',
    'projets/edit'     => 'Modifier le projet',
    'projets/photos'   => 'Photos du projet',
    'categories'       => 'Catégories',
    'pages'            => 'Pages',
    'pages/edit'       => 'Modifier la page',
    default            => 'Admin',
};

$content_file = __DIR__ . '/' . $routes[$route];
require __DIR__ . '/layout.php';
