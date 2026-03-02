<?php
/**
 * Crée le premier utilisateur admin.
 * Usage : php seed_admin.php [login] [password]
 * Par défaut : admin / NbArch2026!
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$login    = $argv[1] ?? 'nbadmin';
$password = $argv[2] ?? 'NbArch2026!';

$hash = password_hash($password, PASSWORD_DEFAULT);

$exists = DB::fetchOne('SELECT id FROM utilisateurs WHERE login = ?', [$login]);

if ($exists) {
    DB::query('UPDATE utilisateurs SET password_hash = ? WHERE login = ?', [$hash, $login]);
    echo "Admin « $login » mis à jour.\n";
} else {
    DB::query('INSERT INTO utilisateurs (login, password_hash) VALUES (?, ?)', [$login, $hash]);
    echo "Admin « $login » créé.\n";
}

echo "Login    : $login\n";
echo "Password : $password\n";
echo "\nChangez ce mot de passe après la première connexion.\n";
