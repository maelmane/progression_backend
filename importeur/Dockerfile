FROM ubuntu:focal

RUN apt update && apt install -y python3 python3-pip libmysqlclient-dev python3-urllib3 git python3-mysqldb && apt autoclean
RUN pip3 --no-input install pyyaml

COPY modules /usr/lib/python3/dist-packages/

CMD python3 -m importeur $SOURCE $DESTINATION