#!/bin/bash

if [ -r /var/www/app/config/worker_config.ini ]; then
    cd /var/www
    setuser www-data php /var/www/vendor/brianlmoon/gearmanmanager/pecl-manager.php -vvv -c /var/www/app/config/worker_config.ini
else
   echo /var/www/app/config/worker_config.ini not found so no gearman workers running
   sleep 600
fi

