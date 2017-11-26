while true
do 
    head -c 1024 /dev/urandom >> /tmp/download.pdf.part
    sleep 1
done

