<?php
/**
 * fix_descriptions.php — Corrige orthographe + majuscules en début de ligne
 * Usage : php fix_descriptions.php
 * Exécuter sur le serveur Infomaniak via SSH.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$projets = DB::fetchAll(
    "SELECT id, titre, description FROM projets WHERE description IS NOT NULL AND description != ''"
);

// --- Corrections orthographiques spécifiques ---
$spelling_fixes = [
    68  => ['mai 20101'                  => 'mai 2010'],
    63  => ['>septembre'                 => '> septembre'],
    11  => ['2005 -2007'                 => '2005 - 2007'],
    99  => ["d'étude parallèles"         => "d'études parallèles"],
    123 => [
        'Etablissements pénitentiaire'   => 'Établissements pénitentiaires',
    ],
];

$count = 0;

foreach ($projets as $p) {
    $desc = $p['description'];
    $original = $desc;

    // 1. Corrections orthographiques
    if (isset($spelling_fixes[$p['id']])) {
        foreach ($spelling_fixes[$p['id']] as $search => $replace) {
            $desc = str_replace($search, $replace, $desc);
        }
    }

    // 2. Supprimer les paragraphes vides <p></p>
    $desc = preg_replace('#<p>\s*</p>#', '', $desc);

    // 3. Supprimer les <br /> en fin de paragraphe (avant </p>)
    $desc = preg_replace('#\s*<br\s*/?>\s*</p>#', '</p>', $desc);

    // 4. Supprimer les espaces multiples
    $desc = preg_replace('#  +#', ' ', $desc);

    // 5. Majuscule après <p> (si lettre minuscule)
    $desc = preg_replace_callback(
        '#(<p>)(\s*)([a-zàâæçéèêëïîôœùûüÿñ])#u',
        function ($m) {
            return $m[1] . $m[2] . mb_strtoupper($m[3], 'UTF-8');
        },
        $desc
    );

    // 6. Majuscule après <br /> ou <br> (si lettre minuscule)
    $desc = preg_replace_callback(
        '#(<br\s*/?>)(\s*)([a-zàâæçéèêëïîôœùûüÿñ])#u',
        function ($m) {
            return $m[1] . $m[2] . mb_strtoupper($m[3], 'UTF-8');
        },
        $desc
    );

    // 7. Trim
    $desc = trim($desc);

    if ($desc !== $original) {
        DB::query("UPDATE projets SET description = ? WHERE id = ?", [$desc, $p['id']]);
        $count++;
        echo "CORRIGÉ : [{$p['id']}] {$p['titre']}\n";
    }
}

echo "\nTotal corrigé : $count projets\n";
echo "\nNote : les liens vers nbarch.com dans les concours (IDs 70, 58, 45) pointent vers l'ancien site Indexhibit.\n";
