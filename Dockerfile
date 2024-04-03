FROM harbor.makeroi.dev/library/base-backend:php8-2
RUN usermod -u 1000 www-data
LABEL maintainer="Leonid Dyukov <l.dyukov@makeroi.ru>"

########################################################
COPY .gitlab/default/scripts/entrypoint.sh /application/entrypoint.sh
RUN chmod 775 /application/entrypoint.sh

COPY .gitlab/default/services/php-configuration/*.ini /usr/local/etc/php/conf.d/
COPY .gitlab/default/services/nginx/ /etc/nginx/
COPY .gitlab/default/services/php-fpm.conf /usr/local/etc/php-fpm.conf

########################################################
COPY composer.json /application/composer.json
COPY composer.lock.tmp /application/composer.lock

#########################################################
COPY --chown=www-data:www-data . /application
RUN sed -i 's/SECLEVEL=2/SECLEVEL=1/g' /etc/ssl/openssl.cnf
RUN composer install --no-ansi --no-interaction --no-progress --optimize-autoloader

CMD ["/bin/sh", "-c", "/application/entrypoint.sh"]

