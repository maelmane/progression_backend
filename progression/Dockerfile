FROM composer:latest AS composer

COPY composer.json /var/www/

RUN composer install -d /var/www/

FROM php:apache

# Mise a jour de la liste de package
RUN apt update && apt upgrade -y && apt autoclean
RUN apt update && apt install -y default-libmysqlclient-dev default-mysql-client python3-yaml libldap2-dev libyaml-dev libzip-dev python3 python3-pip && apt autoclean php7.4-dom

RUN pip3 install progression_qc

#Ajout d'extensions PHP
RUN docker-php-ext-install -j$(nproc) mysqli ldap zip pdo pdo_mysql

COPY docker-php-ext-get /usr/local/bin/

RUN docker-php-source extract &&\
    docker-php-ext-get yaml 2.2.2 &&\
    docker-php-ext-install yaml &&\
    docker-php-source delete

RUN mkdir -p /var/www/progression/
COPY app /var/www/progression/app/
COPY tests /var/www/progression/tests/
COPY db /var/www/progression/db/

RUN mkdir -p /var/www/progression/vendor
COPY --from=composer  /var/www/vendor/ /var/www/progression/vendor/
RUN mkdir -p /var/www/progression/app/storage
RUN chown www-data:www-data /var/www/progression/app/storage

RUN echo AddDefaultCharset utf-8 >> /etc/apache2/apache2.conf
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/progression\/app\/html/' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

WORKDIR /var/www/
RUN chmod +x progression/app/entrypoint.sh
CMD [ "bash", "progression/app/entrypoint.sh" ]

# Installation des beautifiers pour la standardisation du code soumis
RUN apt install -y black
RUN apt install -y clang-format
RUN pip3 install beautysh
RUN apt install -y npm
RUN npm --global install standard typescript ts-standard
RUN touch /tmp/tsconfig.eslint.json

COPY phpstan.neon /var/www/progression/
COPY phpunit.xml /var/www/progression/