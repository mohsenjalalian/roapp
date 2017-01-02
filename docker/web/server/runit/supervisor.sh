#!/bin/bash

if [ -r /etc/supervisor.conf ]; then
    exec supervisord -n -c /etc/supervisor.conf
else
   echo /etc/supervisor.conf not found so no supervisor running
   sleep 600
fi
