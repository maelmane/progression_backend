FROM php:7

# Install git, the php image doesn't have installed
RUN apt update -yqq && apt install git emacs elpa-htmlize unzip python -yqq && apt autoclean

# Install composer
RUN curl -sS https://getcomposer.org/installer | php

COPY install.el /tmp/
RUN emacs --no-init-file --script /tmp/install.el
