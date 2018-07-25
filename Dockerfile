FROM greyltc/lamp

#Corrige https://bugs.archlinux.org/task/58100
RUN sed -i  '15s/\/etc\/ssl/\"\/etc\/ssl\"/' /usr/sbin/c_rehash
RUN sed -i  '16s/\/usr/"\/usr"/' /usr/sbin/c_rehash

RUN rm /srv/http/info.php

# Mise a jour de la liste de package
RUN pacman -Suy --noconfirm
RUN pacman -S --noprogressbar --noconfirm --needed unzip wget
RUN echo extension=gmp.so >> /etc/php/php.ini

RUN wget http://codemirror.net/codemirror.zip
RUN unzip -d /srv/http/ codemirror.zip
RUN mv /srv/http/codemirror* /srv/http/CodeMirror

#Pour authentification par LDAP
RUN mkdir /etc/ldap
COPY ldap.conf /etc/ldap
COPY certs/* /etc/ssl/certs/
RUN c_rehash

#Pour une BD à même le conteneur
COPY build_db.sh /tmp
COPY questions /tmp/questions
COPY *.sql /tmp/
RUN start-servers& sleep 5 && cd /tmp && ./build_db.sh

COPY quiz.conf /srv/
RUN chown -R http:http /srv/quiz.conf
COPY app/ /srv/http/

