FROM composer:1.7.2 as composer

WORKDIR /app/
COPY composer.json /app/
COPY composer.lock /app/
RUN composer install

FROM php:7.2-apache

COPY ./ /var/www/html/
COPY --from=composer /app/ /var/www/html/

RUN docker-php-ext-install pdo_mysql
ADD mysql/confDocker.ini /var/www/html/src/conf/conf.ini

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
ADD apache/apache-config.conf /etc/apache2/sites-enabled/000-default.conf