<?php

require('../quiz.php');

$r=array(rand(10,100),rand(10,100),rand(10,100),rand(10,100),rand(10,100));

execute("Question 2", "Fantastique! Maintenant ajoutons la possibilité d\'envoyer une série de paquets et de faire des statistiques.", "
64 octets recus de 8.8.8.8 : temps ecoule=".(2*$r[0])." ms
64 octets recus de 8.8.8.8 : temps ecoule=".(2*$r[1])." ms
64 octets recus de 8.8.8.8 : temps ecoule=".(2*$r[2])." ms
64 octets recus de 8.8.8.8 : temps ecoule=".(2*$r[3])." ms
64 octets recus de 8.8.8.8 : temps ecoule=".(2*$r[4])." ms
rtt min/moy/max = ".strval(min($r)*2)."/".strval(round(array_sum($r)*2/count($r)))."/".strval(max($r)*2)."ms", "PBElot9R2M", '
import time

def envoyerICMP( destination ):
    """
    Envoie un paquet ICMP à un hôte distant et calcule le temps écoulé avant de recevoir la réponse.

    Paramètre :
    - destination : str, l\'adresse IP d\'un hôte distant.

    Retour : le temps écoulé entre l\'envoi et la réception de la réponse en millisecondes.

    """
    #Instancie le paquet ICMP
    paquet_envoyé = ICMP( destination )

    #Ouvre un socket vers la destination
    socket = open_socket( destination )

    #Chronomètre le temps de l\'aller-retour
    heure_envoi = time.time()

    #Envoi le paquet et attend la réponse
    socket.send_packet( paquet_envoyé )
    paquet_reçu = socket.receive_packet()

    #Retourne le temps écoulé en ms.
    return time.time()-heure_envoi

def ping( destination, nb_paquets ):
    """
    Teste l\'accessibilité d\'un hôte distant.
 
    ping envoie un ou plusieurs paquets ICMP et affiche le nombre de millisecondes nécessaires pour l\'aller-retour de chaque paquet
    ainsi que le temps minimal, moyen et maximal sous la forme : 
    64 octets reçus de [destination] : temps écoulé=[x] ms

    rtt min/moy/max = [min]/[moy]/[max] ms

    où destination est l\'adresse IP de l\'hôte distant
       x est le nombre de millisecondes écoulées (arrondi à la ms près).
       min est le nombre minimal de millisecondes écoulées (arrondi à la ms près).
       moy est le nombre moyen de millisecondes écoulées (arrondi à la ms près).
       max est le nombre maximal de millisecondes écoulées (arrondi à la ms près).
 
    Paramètre :
    - destination : str, l\'adresse IP d\'un hôte distant.
    - nb_paquets : entier, le nombre de paquets ICMP à envoyer.
 
    """',
'',

'
#Ping google.com
ping( "8.8.8.8", 5 )
', '
from time import sleep

class ICMP:
 def __init__(self,n):
  pass

class Socket:

 no=0
 nbs=['.$r[0].','.$r[1].','.$r[2].','.$r[3].','.$r[4].']
 def send_packet(self,x):
  sleep(Socket.nbs[Socket.no]/1000)
 def receive_packet(self):
  sleep(Socket.nbs[Socket.no]/1000)
  Socket.no+=1
  
def open_socket( x ):
 return Socket()
 
', "q3-xqbzlrhohr.php");

?>
