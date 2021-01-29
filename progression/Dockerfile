FROM php:apache

#Corrige https://bugs.archlinux.org/task/58100
#RUN sed -i  '15s/\/etc\/ssl/\"\/etc\/ssl\"/' /usr/sbin/c_rehash
#RUN sed -i  '16s/\/usr/"\/usr"/' /usr/sbin/c_rehash

# Mise a jour de la liste de package
RUN apt update && apt upgrade -y && apt autoclean
RUN apt update && apt install -y unzip wget default-libmysqlclient-dev python-yaml git && apt autoclean

#Ajout d'extensions PHP
RUN docker-php-ext-install -j$(nproc) mysqli
#RUN echo extension=gmp.so >> /etc/php/php.ini

#Pour authentification par LDAP
COPY ldap.conf /etc/ldap
COPY certs/* /etc/ssl/certs/
RUN c_rehash

COPY quiz.conf /etc/
COPY app /var/www/html/

RUN chown -R www-data:www-data /var/www/html /etc/quiz.conf

RUN echo AddDefaultCharset utf-8 >> /etc/apache2/apache2.conf

