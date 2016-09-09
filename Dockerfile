FROM phusion/baseimage:0.9.19
MAINTAINER Amir <amir.modarresi.13@gmail.com>

# Apache
RUN apt-get update
RUN apt-get -y install apache2

# PHP
RUN apt-get -y install python-software-properties
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update
RUN apt-get -y --force-yes install php7.0
RUN apt-get -y --force-yes install libapache2-mod-php7.0 php7.0-curl php7.0-json php7.0-pgsql
RUN apt-get update && apt-get install php7.0-xml -y --allow-unauthenticated

# Apache service run
RUN mkdir /etc/service/apache
ADD docker/apache.sh /etc/service/apache/run
RUN chmod +x /etc/service/apache/run

# Enable SSH
RUN rm -f /etc/service/sshd/down

# Regenerate SSH host keys. baseimage-docker does not contain any, so you
# have to do that yourself. You may also comment out this instruction; the
# init system will auto-generate one during boot.
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh
RUN /usr/sbin/enable_insecure_key

# Enable ACL
RUN apt-get install acl

RUN usermod -u 1000 www-data

#ADD ./ /var/www/html/


# Symfony Initialization

WORKDIR /var/www/html

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
