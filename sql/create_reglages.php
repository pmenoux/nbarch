<?php
// Crée la table reglages (clé/valeur) et insère les valeurs par défaut
// Usage : php sql/create_reglages.php (depuis la racine du site)

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

DB::query("
    CREATE TABLE IF NOT EXISTS reglages (
        cle VARCHAR(50) PRIMARY KEY,
        valeur TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Valeur par défaut : NULL = premier projet publié (comportement existant)
$exists = DB::fetchOne("SELECT 1 FROM reglages WHERE cle = 'accueil_projet_id'");
if (!$exists) {
    DB::query("INSERT INTO reglages (cle, valeur) VALUES ('accueil_projet_id', NULL)");
}

echo "Table reglages créée.\n";
