#!/bin/sh

if [ -f "/data/config/ssl/foodpanda.com.intermediate.chain.pem" ]; then
    mkdir -p /etc/syslog-ng/cert.d/
    cp /data/config/ssl/foodpanda.com.intermediate.chain.pem /etc/syslog-ng/cert.d/
else
    echo "No certificate set at /data/config/ssl/foodpanda.com.intermediate.chain.pem"
fi
