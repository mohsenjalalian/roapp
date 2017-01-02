#!/bin/bash
setuser www-data bin/console cache:clear --env=prod
setuser www-data bin/console cache:clear --env=dev
setuser www-data bin/console doctrine:database:create
setuser www-data bin/console doctrine:schema:update --force
cd /var/www/src/AppBundle/Resources/public && setuser www-data bower install
cd /var/www/ && setuser www-data bin/console assets:install --symlink
cd /var/www/ && setuser www-data bin/console assetic:dump