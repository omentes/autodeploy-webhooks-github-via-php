cd /var/www/alpha.santaplantas.com
git fetch origin alpha
git reset --hard origin/alpha
chown -R www-data:www-data .
git checkout alpha
