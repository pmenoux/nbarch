<?php
/**
 * Debug : teste les regex de fix_descriptions sur le premier projet.
 * Usage : php debug_fix.php
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$p = DB::fetchOne(
    "SELECT id, titre, description FROM projets WHERE description IS NOT NULL AND description != '' LIMIT 1"
);

if (!$p) {
    echo "Aucun projet avec description.\n";
    exit;
}

echo "Projet #{$p['id']} : {$p['titre']}\n";
echo "Description brute (premiers 300 chars) :\n";
echo substr($p['description'], 0, 300) . "\n\n";

$desc = $p['description'];

// Test regex <p>
$count1 = preg_match_all('#(<p>)(\s*)([a-zàâæçéèêëïîôœùûüÿñ])#u', $desc, $matches1);
echo "Regex <p>+lowercase : $count1 match(es)\n";
if ($count1 > 0) {
    echo "  Matches : " . implode(', ', $matches1[3]) . "\n";
}
if ($count1 === false) {
    echo "  ERREUR PCRE : " . preg_last_error() . "\n";
}

// Test regex <br>
$count2 = preg_match_all('#(<br\s*/?>)(\s*)([a-zàâæçéèêëïîôœùûüÿñ])#u', $desc, $matches2);
echo "Regex <br>+lowercase : $count2 match(es)\n";
if ($count2 > 0) {
    echo "  Matches : " . implode(', ', $matches2[3]) . "\n";
}
if ($count2 === false) {
    echo "  ERREUR PCRE : " . preg_last_error() . "\n";
}

// Test empty paragraphs
$count3 = preg_match_all('#<p>\s*</p>#', $desc, $matches3);
echo "Paragraphes vides : $count3\n";

// Test trailing br
$count4 = preg_match_all('#\s*<br\s*/?>\s*</p>#', $desc, $matches4);
echo "Trailing <br> avant </p> : $count4\n";

// Hex dump des 50 premiers octets
echo "\nHex dump (50 premiers octets) :\n";
for ($i = 0; $i < min(50, strlen($desc)); $i++) {
    printf('%02X ', ord($desc[$i]));
    if (($i + 1) % 16 === 0) echo "\n";
}
echo "\n";
