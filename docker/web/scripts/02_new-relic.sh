#!/bin/sh

if [ -e "/data/config/newRelicConfig.json" ]; then
    #seting NewRelic Key as env variable
    NEW_RELIC_LICENSE_KEY=$(cat /data/config/newRelicConfig.json | jq -r '.NEW_RELIC_LICENSE_KEY')
    NEW_RELIC_APP_NAME="${ENVIRONMENT}-${COUNTRY_CODE}-${APP_NAME}"

    NR_INSTALL_SILENT=true newrelic-install install

    rm -rf /etc/php/7.0/fpm/conf.d/newrelic.ini /etc/php/7.0/cli/conf.d/newrelic.ini

    NR_FPM_CONFIGS="/etc/php/7.0/fpm/conf.d/20-newrelic.ini"
    NR_CLI_CONFIGS="/etc/php/7.0/cli/conf.d/20-newrelic.ini"

    sed -i -e "s/newrelic.license\s*=.*/newrelic.license = '${NEW_RELIC_LICENSE_KEY}'/g" ${NR_FPM_CONFIGS} \
        ${NR_CLI_CONFIGS}
    sed -i -e "s/newrelic.appname\s*=.*/newrelic.appname = '${NEW_RELIC_APP_NAME}'/g" ${NR_FPM_CONFIGS} \
        ${NR_CLI_CONFIGS}
else
    echo "No new relic config file found at /data/config/newRelicConfig.json"
fi
