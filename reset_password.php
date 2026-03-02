<?php
/**
 * Reset admin password — SUPPRIMER CE FICHIER APRÈS UTILISATION
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$login    = 'nbadmin';
$password = 'NbArch2026!';
$hash     = password_hash($password, PASSWORD_DEFAULT);

DB::query('UPDATE utilisateurs SET password_hash = ? WHERE login = ?', [$hash, $login]);

echo "Mot de passe réinitialisé pour « $login ».\n";
echo "SUPPRIMEZ CE FICHIER IMMÉDIATEMENT.\n";
