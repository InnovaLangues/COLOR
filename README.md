##Download vendors, update schema and assets install

chmod 777 app/cache
chmod 777 app/logs
rm -rf app/cache/*
rm -rf app/logs/*
sudo php app/console cache:clear --env=prod
sudo php update composer.phar
sudo php app/console assets:install --symlink
sudo php app/console assetic:dump
php app/console doctrine:database:create
php app/console doctrine:schema:update --dump-sql
php app/console doctrine:schema:update --force

## Author

* Mahmoud Charfeddine (MahmoudCharfeddine)
  charfeddine.mahmoud@gmail.com
