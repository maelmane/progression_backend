#Modifié par Mikael Nadeau le 15/02/2018
#Fait par Nicolas Bazinet et Marc-Antoine Barabé le 26/10/2017

#On stop les services susceptibles de causer problèmes

#VMWare Workstation
systemctl stop vmware
systemctl stop vmware-workstation-server

#Vérifie si le conteneur est en fonction, on l'arrête s'il l'est
if ($(docker inspect -f '{{.State.Running}}' quiz)); then
    docker stop quiz
fi

#Suppression du conteneur
if((docker ps -a | grep quiz | wc -l)  > 0 ); then
    docker rm /quiz
fi

#Build le conteneur
docker build -t quiz .


#Run le conteneur
docker run -d --name quiz -p 443:443 quiz
