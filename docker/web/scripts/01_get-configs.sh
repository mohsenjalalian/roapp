#!/bin/sh

if [ -z "$CONFIGURATION_BUCKET" ]; then
    echo "No config url set"
else
    mkdir -p /data/config/
    aws s3 sync s3://$CONFIGURATION_BUCKET /data/config/ --delete
    cp -f /data/config/parameters/parameters.yml /var/www/app/config/parameters/
fi


