FROM greyltc/lamp

RUN pacman -Sy --noconfirm
RUN pacman -S --noprogressbar --noconfirm --needed unzip wget

COPY build_db.sh /tmp
COPY questions /tmp/questions
COPY create_db.sql /tmp

RUN start-servers& sleep 3 && cd /tmp && ./build_db.sh

RUN wget http://codemirror.net/codemirror.zip
RUN unzip -d /srv/http/ codemirror.zip
RUN mv /srv/http/codemirror* /srv/http/CodeMirror

COPY db.conf /srv/
#RUN chown -R www-data:www-data /srv/*
COPY app/ /srv/http/

