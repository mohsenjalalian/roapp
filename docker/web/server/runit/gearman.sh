#!/usr/bin/env bash

# gearman jobs will remain on the server forever unless a limit is specified
# http://gearman.org/manual/job_server/
exec gearmand --job-retries=3
