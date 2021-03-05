FROM php:8

# Install git, the php image doesn't have installed
RUN apt update
RUN DEBIAN_FRONTEND=noninteractive TZ=America/Toronto apt install tzdata git emacs elpa-htmlize unzip python3 python3-pip libyaml-dev -yqq && apt autoclean
RUN pip3 install napkin

COPY docker-php-ext-get /usr/local/bin/

RUN docker-php-source extract &&\
    docker-php-ext-get yaml 2.2.1 &&\
    docker-php-ext-install yaml &&\
    docker-php-source delete

COPY init.el /root/.emacs.el
COPY install.el /tmp/
RUN emacs --no-init-file --script /tmp/install.el

# Install composer
RUN curl -sS https://getcomposer.org/installer | php
