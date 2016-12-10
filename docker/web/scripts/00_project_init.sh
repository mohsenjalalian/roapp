#!/bin/sh

rm var/cache/* -rf
cd src/AppBundle/Resources/public;
setuser www-data bower install
cd ../../../../
setuser www-data php bin/console doctrine:schema:update --force
setuser www-data php bin/console assets:install
setuser www-data php bin/console assetic:dump --env=prod
setuser www-data php bin/console assetic:dump --env=dev
setuser www-data php bin/console doctrine:schema:update --force
setuser www-data php bin/console cache:clear --env=prod
setuser www-data php bin/console cache:clear --env=dev