<?php

require('../quiz.php');

$r=rand(10,100);

execute("Question 1", "Encore une fois, vous tentez de reproduire un outils commun en Python (peut-être dans l\'optique de développer PythonOS). Cette fois, vous vous attaquez à ping. Cette commande permet de tester la connectivité d\'un hôte distant. Vous avez déjà terminé la fonction <code>envoyerICMP</code> qui envoit un paquet ICMP (un paquet de test) à l\'hôte et calcule le temps requis pour recevoir une réponse. Faites maintenant la fonction <code>ping</code> elle-même.", "64 octets recus de 8.8.8.8 : temps ecoule=".(2*$r)." ms", "TfcRivhyWt", '
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

def ping( destination ):
    """
    Teste l\'accessibilité d\'un hôte distant.
 
    ping envoie un paquet ICMP et affiche le nombre de millisecondes nécessaires pour l\'aller-retour du paquet
    sous la forme : 
    64 octets reçus de [destination] : temps écoulé=[x] ms

    où destination est l\'adresse IP de l\'hôte distant
       x est le nombre de millisecondes écoulées (arrondi à la ms près).
 
    Paramètre :
    - destination : str, l\'adresse IP d\'un hôte distant.
 
    """',
'',

'
#Ping google.com
ping( "8.8.8.8" )
', '
from time import sleep

class ICMP:
 def __init__(self,n):
  pass

class Socket:
 def send_packet(self,x):
  sleep('.$r.'/1000)
 def receive_packet(self):
  sleep('.$r.'/1000)
  
def open_socket( x ):
 return Socket()
 
', "q2-ufzxedwnht.php");

?>
