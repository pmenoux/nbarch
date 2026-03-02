<?php
/**
 * Dump toutes les descriptions des projets pour vérification orthographique.
 * Usage : php dump_descriptions.php
 * Exécuter sur le serveur Infomaniak via SSH.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$projets = DB::fetchAll(
    "SELECT p.id, p.titre, p.slug, p.description, c.nom AS categorie
     FROM projets p
     JOIN categories c ON c.id = p.categorie_id
     WHERE p.statut = 'publié'
     ORDER BY c.ordre, p.ordre"
);

echo "=== DESCRIPTIONS DES PROJETS ===\n\n";

foreach ($projets as $p) {
    echo str_repeat('=', 60) . "\n";
    echo "ID: {$p['id']} | {$p['categorie']} | {$p['titre']}\n";
    echo str_repeat('-', 60) . "\n";

    $desc = $p['description'] ?? '(vide)';
    // Afficher le HTML brut pour repérer les problèmes
    echo $desc . "\n\n";
}

echo "Total : " . count($projets) . " projets\n";
