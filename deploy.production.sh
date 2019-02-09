cd example.com
git fetch origin master
git reset --hard origin/master
chown -R www-data:www-data .
git checkout master
