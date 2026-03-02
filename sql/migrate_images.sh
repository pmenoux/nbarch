#!/bin/bash
# migrate_images.sh — Copie les images Indexhibit vers le nouveau site
# À exécuter via SSH : bash migrate_images.sh
#
# Prérequis : migration SQL déjà exécutée (projets + photos en base)

SOURCE=~/nbweb/files/gimgs
DEST=~/sites/26.nbarch.com/uploads/projets

DB_HOST="gfeu.myd.infomaniak.com"
DB_USER="gfeu_nbarch"
DB_PASS="BovardNabarch1313!"
DB_NAME="gfeu_nbarch"

echo "=== Migration images Indexhibit → nbarch ==="
echo "Source : $SOURCE"
echo "Dest   : $DEST"
echo ""

OK=0
ERR=0

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
  --batch --skip-column-names \
  -e "SELECT media_ref_id, media_file FROM ndxz_media WHERE media_hide=0 ORDER BY media_ref_id" \
| while IFS=$'\t' read projet_id filename; do
    mkdir -p "$DEST/$projet_id"
    if cp "$SOURCE/$filename" "$DEST/$projet_id/$filename" 2>/dev/null; then
        echo "OK  $projet_id/$filename"
        ((OK++))
    else
        echo "ERR $projet_id/$filename (source introuvable)"
        ((ERR++))
    fi
done

echo ""
echo "=== Terminé — OK: $OK | ERR: $ERR ==="
