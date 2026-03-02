<?php
/**
 * Réinitialise le mot de passe admin.
 * À SUPPRIMER après utilisation.
 * Accès : https://26.nbarch.com/reset-admin.php
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$login    = 'nbadmin';
$password = 'NbArch2026!';
$hash     = password_hash($password, PASSWORD_DEFAULT);

$exists = DB::fetchOne('SELECT id FROM utilisateurs WHERE login = ?', [$login]);

if ($exists) {
    DB::query('UPDATE utilisateurs SET password_hash = ? WHERE login = ?', [$hash, $login]);
    echo "Admin « $login » — mot de passe réinitialisé.<br>";
} else {
    DB::query('INSERT INTO utilisateurs (login, password_hash) VALUES (?, ?)', [$login, $hash]);
    echo "Admin « $login » créé.<br>";
}

echo "<br><strong>Login :</strong> $login<br>";
echo "<strong>Password :</strong> $password<br>";
echo "<br><em>SUPPRIMEZ ce fichier immédiatement après usage.</em>";
