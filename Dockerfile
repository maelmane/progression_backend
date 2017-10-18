FROM greyltc/lamp


COPY build_db.sh /tmp
COPY questions /tmp/questions
COPY create_db.sql /tmp

RUN start-servers& sleep 3 && cd /tmp && ./build_db.sh

COPY app/ /srv/http/

COPY db.conf /srv/
#RUN chown -R www-data:www-data /srv/*

