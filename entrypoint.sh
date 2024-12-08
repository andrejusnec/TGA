#!/bin/bash
# Fix ownership and permissions for writable directories
chown -R www-data:www-data /var/www/html/var /var/www/html/public /var/www/html/config
chmod -R 775 /var/www/html/var /var/www/html/public /var/www/html/config

# Execute the container's main process
exec "$@"
