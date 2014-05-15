##Download vendors, update schema and assets install

* sudo chmod 777 app/cache
* sudo chmod 777 app/logs
* sudo rm -rf app/cache/*
* sudo rm -rf app/logs/*
* sudo php app/console cache:clear --env=prod
* sudo php update composer.phar
* sudo php app/console assets:install --symlink
* sudo php app/console assetic:dump
* sudo php app/console doctrine:database:create
* sudo php app/console doctrine:schema:update --dump-sql
* sudo php app/console doctrine:schema:update --force

## Author

* Mahmoud Charfeddine (MahmoudCharfeddine)
  charfeddine.mahmoud@gmail.com
