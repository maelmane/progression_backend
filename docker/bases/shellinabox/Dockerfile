FROM ubuntu:xenial

ENV SIAB_VERSION=2.19 

RUN apt-get update && apt-get install -y openssl curl openssh-client sudo locales screen \
      shellinabox=${SIAB_VERSION} && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
  ln -sf '/etc/shellinabox/options-enabled/00+Black on White.css' \
    /etc/shellinabox/options-enabled/00+Black-on-White.css && \
  ln -sf '/etc/shellinabox/options-enabled/00_White On Black.css' \
    /etc/shellinabox/options-enabled/00_White-On-Black.css && \
  ln -sf '/etc/shellinabox/options-enabled/01+Color Terminal.css' \
    /etc/shellinabox/options-enabled/01+Color-Terminal.css

RUN locale-gen fr_CA.UTF-8

ENV SIAB_USERCSS="Reverse:+/etc/shellinabox/options-enabled/00+Black-on-White.css,Normal:-/etc/shellinabox/options-enabled/00_White-On-Black.css;Colors:+/etc/shellinabox/options-enabled/01+Color-Terminal.css,Monochrome:-/etc/shellinabox/options-enabled/01_Monochrome.css" \
  SIAB_PORT=4200 \
  SIAB_ADDUSER=false \
  SIAB_USER=guest \
  SIAB_USERID=1000 \
  SIAB_GROUP=guest \
  SIAB_GROUPID=1000 \
  SIAB_PASSWORD=guest \
  SIAB_SHELL=/bin/bash \
  SIAB_HOME=/home/guest \
  SIAB_SUDO=false \
  SIAB_SSL=false \
  SIAB_SERVICE=/:LOGIN \
  SIAB_PKGS=none \
  SIAB_TIMEOUT=0 \
  SIAB_SCRIPT=none

EXPOSE 4200

ADD assets/entrypoint.sh /usr/local/sbin/

#Enlève la confirmation de quitter la page
RUN sed -i 's/beforeunload/bbbbbbunload/' /usr/bin/shellinaboxd

ENTRYPOINT ["entrypoint.sh"]
CMD ["shellinabox"]

