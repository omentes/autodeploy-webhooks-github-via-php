cd /var/www/beta.santaplantas.com
git fetch origin beta
git reset --hard origin/beta
chown -R www-data:www-data .
