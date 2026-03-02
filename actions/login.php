<?php
// POST login — vérifie identifiant/mot de passe, crée la session

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

csrf_check();

$login    = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if ($login === '' || $password === '') {
    flash('Identifiant et mot de passe requis.', 'err');
    redirect(APP_URL . '/nbadmin/login');
}

$user = DB::fetchOne('SELECT * FROM utilisateurs WHERE login = ?', [$login]);

if (!$user || !password_verify($password, $user['password_hash'])) {
    flash('Identifiant ou mot de passe incorrect.', 'err');
    redirect(APP_URL . '/nbadmin/login');
}

// Connexion réussie
session_regenerate_id(true);
$_SESSION['admin_id'] = $user['id'];

redirect(APP_URL . '/nbadmin/');
