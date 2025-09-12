#!/bin/bash

# Automatische permission fix script
# Draait elke 15 minuten om permission problemen te voorkomen

WEBSITE_DIR="/var/www/thilacoloma"
LOG_FILE="/var/log/thilacoloma-permissions.log"

echo "[$(date)] Starting automatic permission check..." >> $LOG_FILE

# Controleer of er bestanden zijn die niet door www-data owned worden
WRONG_OWNER=$(find $WEBSITE_DIR/storage -not -user www-data 2>/dev/null | wc -l)

if [ $WRONG_OWNER -gt 0 ]; then
    echo "[$(date)] Found $WRONG_OWNER files with wrong ownership, fixing..." >> $LOG_FILE
    
    # Fix ownership en permissions
    chown -R www-data:www-data $WEBSITE_DIR/storage/
    chown -R www-data:www-data $WEBSITE_DIR/bootstrap/cache/
    
    # Set correct permissions
    find $WEBSITE_DIR/storage -type d -exec chmod 775 {} \;
    find $WEBSITE_DIR/storage -type f -exec chmod 664 {} \;
    find $WEBSITE_DIR/bootstrap/cache -type d -exec chmod 775 {} \;
    find $WEBSITE_DIR/bootstrap/cache -type f -exec chmod 664 {} \;
    
    echo "[$(date)] Permissions fixed automatically" >> $LOG_FILE
else
    echo "[$(date)] All permissions OK" >> $LOG_FILE
fi
