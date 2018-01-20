FROM greyltc/lamp

# Mise a jour de la liste de package
RUN pacman -Sy --noconfirm
RUN pacman -S --noprogressbar --noconfirm --needed unzip wget

#COPY build_db.sh /tmp
#COPY questions /tmp/questions
#COPY *.sql /tmp/

#RUN start-servers& sleep 3 && cd /tmp && ./build_db.sh

RUN wget http://codemirror.net/codemirror.zip
RUN unzip -d /srv/http/ codemirror.zip
RUN mv /srv/http/codemirror* /srv/http/CodeMirror

RUN mkdir /etc/ldap
COPY ldap.conf /etc/ldap
COPY certs/ad_cert.cer /etc/ssl/certs/
RUN c_rehash

COPY db.conf /srv/
RUN chown -R http:http /srv/db.conf
COPY app/ /srv/http/

