#!/bin/bash

# Configuration : Utilisation d'un dossier "backups" dans le projet local
BACKUP_DIR="./backups"
DATE=$(date +%Y-%m-%d_%H%M%S)

# Noms de votre conteneur et BDD locaux
DB_CONTAINER="symfony_db"
DB_NAME="helpdesk_db"
DB_USER="root"
DB_PASS="root_password"

# Création du dossier local s'il n'existe pas
mkdir -p $BACKUP_DIR

# Génération du dump compressé
docker exec $DB_CONTAINER mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Suppression des sauvegardes locales de plus de 30 jours
find $BACKUP_DIR -type f -name "*.sql.gz" -mtime +30 -delete

echo "Sauvegarde réussie dans : $BACKUP_DIR/db_$DATE.sql.gz"