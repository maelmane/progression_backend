FROM php:8

# Install git, the php image doesn't have installed
RUN apt update
RUN DEBIAN_FRONTEND=noninteractive TZ=America/Toronto apt install tzdata git emacs elpa-htmlize unzip python3 python3-pip -yqq && apt autoclean
RUN pip3 install napkin

COPY init.el /root/.emacs.el
COPY install.el /tmp/
RUN emacs --no-init-file --script /tmp/install.el

# Install composer
RUN curl -sS https://getcomposer.org/installer | php
