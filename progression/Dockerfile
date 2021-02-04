FROM composer:latest
RUN mkdir /var/www/vendor/
COPY composer.json /var/www/vendor/

RUN composer install -d /var/www/vendor/

FROM php:apache

COPY --from=0  /var/www/vendor /var/www/

# Mise a jour de la liste de package
RUN apt update && apt upgrade -y && apt autoclean
RUN apt update && apt install -y unzip wget default-libmysqlclient-dev python-yaml git && apt autoclean

#Ajout d'extensions PHP
RUN docker-php-ext-install -j$(nproc) mysqli

#Pour authentification par LDAP
COPY ldap.conf /etc/ldap
COPY certs/* /etc/ssl/certs/
RUN c_rehash

COPY quiz.conf /etc/
COPY app /var/www/html/

RUN chown -R www-data:www-data /var/www/html /etc/quiz.conf

RUN echo AddDefaultCharset utf-8 >> /etc/apache2/apache2.conf

