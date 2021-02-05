FROM composer:latest AS composer

COPY composer.json /var/www/

RUN composer install -d /var/www/

FROM php:apache

RUN mkdir -p /var/www/storage/logs
RUN mkdir -p /var/www/vendor

# Mise a jour de la liste de package
RUN apt update && apt upgrade -y && apt autoclean
RUN apt update && apt install -y unzip wget default-libmysqlclient-dev python-yaml git libldap2-dev && apt autoclean

#Ajout d'extensions PHP
RUN docker-php-ext-install -j$(nproc) mysqli ldap

#Pour authentification par LDAP
COPY ldap.conf /etc/ldap
COPY certs/* /etc/ssl/certs/
RUN c_rehash

COPY quiz.conf /etc/
COPY app /var/www/
COPY --from=composer  /var/www/vendor/ /var/www/vendor/

RUN chown -R www-data:www-data /var/www/ /etc/quiz.conf

RUN echo AddDefaultCharset utf-8 >> /etc/apache2/apache2.conf
RUN a2enmod rewrite
