#Fait par Nicolas Bazinet et Marc-Antoine Barabé
#26/10/2017

#le docker doit se nommer 'quiz'

#Vérifie sur le docker est démarré puis le stop s'il le faut.
if ($(docker inspect -f '{{.State.Running}}' quiz)); then
    docker stop quiz
fi

#Vérifie si le container existe puis l'efface s'il le faut.
if((docker ps -a | grep quiz | wc -l)  > 0 ); then
    docker rm /quiz
fi

#Build puis run le container
docker build -t quiz .

docker run -d --name quiz -p 443:443 quiz
