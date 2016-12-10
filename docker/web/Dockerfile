FROM phusion/baseimage:0.9.19

RUN locale-gen en_US.UTF-8
ENV LANG       en_US.UTF-8
ENV LC_ALL     en_US.UTF-8
ENV HOME /root

CMD ["/sbin/my_init"]

# installing packages
RUN apt-get update && DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes wget \
    # NewRelic repositories
    && echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' | tee /etc/apt/sources.list.d/newrelic.list \
    && wget -O- https://download.newrelic.com/548C16BF.gpg | apt-key add - \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get update \
    && DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes \
        wget \
        curl \
        php7.0-fpm \
        php7.0-curl \
        php7.0-gearman \
        php7.0-xdebug \
        php7.0-xml \
        gearman-job-server \
        jq \
        unzip \
        supervisor \
        newrelic-php5 \
        nginx \
    && mkdir -p /tmp_download /data/config \
    && mkdir -p /data/log \
    && curl "https://s3.amazonaws.com/aws-cli/awscli-bundle.zip" -o "/tmp_download/awscli-bundle.zip" \
    && unzip -o -d /tmp_download/ /tmp_download/awscli-bundle.zip \
    && python3 /tmp_download/awscli-bundle/install -i /usr/local/aws -b /usr/local/bin/aws \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN apt-get update && \
    DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes php7.0-pgsql

RUN DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes nodejs npm git git-core && \
    npm install -g bower

RUN usermod -u 1000 www-data

# misc commands and configs
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/7.0/fpm/php.ini \
    && sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini \
    && echo "daemon off;" >> /etc/nginx/nginx.conf \
    && sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/7.0/fpm/php-fpm.conf \
    && sed -i -e "s/error_log\s*=\s*\/var\/log\/php7.0-fpm.log/error_log = \/data\/log\/php7.0-fpm.log/g" /etc/php/7.0/fpm/php-fpm.conf \
    && sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini \
    && sed -i -e "s#pid\s*=\s*/run/php/php7.0-fpm.pid#pid = /run/phpfpm.pid#g" /etc/php/7.0/fpm/php-fpm.conf \
    && sed -i -e "s/#\sserver_names_hash_bucket_size\s64;/server_names_hash_bucket_size 128;/g" /etc/nginx/nginx.conf \
    && mkdir -p /var/www /etc/my_init.d /etc/service/nginx /etc/service/phpfpm

# conf files
COPY server/sites-enabled/* /etc/nginx/sites-enabled/
COPY server/foodpanda-kibana.conf /etc/syslog-ng/conf.d/foodpanda-kibana.conf
COPY server/pool.d/x-custom.conf /etc/php/7.0/fpm/pool.d/x-custom.conf

# services
COPY server/runit/nginx.sh  /etc/service/nginx/run
COPY server/runit/phpfpm.sh /etc/service/phpfpm/run
COPY server/runit/gearman.sh /etc/service/gearman/run
#COPY server/runit/workers.sh /etc/service/workers/run
COPY server/runit/supervisor.sh /etc/service/supervisor/run

# startup scripts
COPY ./scripts/00_project_init.sh /etc/my_init.d/00_project_init.sh
COPY ./scripts/01_get-configs.sh /etc/my_init.d/01_get-configs.sh
#COPY ./scripts/02_new-relic.sh /etc/my_init.d/02_new-relic.sh
#COPY ./scripts/03_configure-logging.sh /etc/my_init.d/03_configure-logging.sh
#COPY ./scripts/04_enable-xdebug-if-dev.sh /etc/my_init.d/04_enable-xdebug-if-dev.sh

WORKDIR /var/www

# permissions and owner files changes
RUN chmod +x /etc/service/nginx/run \
    && chmod +x /etc/service/phpfpm/run \
    && chmod +x /etc/service/gearman/run \
    && chmod +x /etc/service/supervisor/run \
    && chmod +x /etc/my_init.d/* \
    && chown -R www-data:www-data . /data/log

EXPOSE 80
