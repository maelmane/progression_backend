<?php

require('../quiz.php');


$nb_perdus=0;
while ($nb_perdus==0 or $nb_perdus==count($r)){
    $r=array(rand(10,100),rand(10,100),rand(10,100),rand(10,100),rand(10,100));
    $min=100;
    $max=0;
    $somme=0;
    $nb_perdus=0;
    for ($i=0;$i<count($r);$i++){
      if ($r[$i]>70){
        $nb_perdus++;
      }
      else{
        $min=min($min,$r[$i]);
        $max=max($max,$r[$i]);
        $somme+=$r[$i];
      }
    }
}

execute("Question 3", "Que faire si on ne reçoit jamais de réponse de l\'hôte? Dans ce cas, une <code>envoyerICMP</code> lance une exception. Comptez le nombre de paquets perdus.", "
64 octets recus de 8.8.8.8 : ".($r[0]>70?"perdu.":"temps ecoule=".(2*$r[0])." ms")."
64 octets recus de 8.8.8.8 : ".($r[1]>70?"perdu.":"temps ecoule=".(2*$r[1])." ms")."
64 octets recus de 8.8.8.8 : ".($r[2]>70?"perdu.":"temps ecoule=".(2*$r[2])." ms")."
64 octets recus de 8.8.8.8 : ".($r[3]>70?"perdu.":"temps ecoule=".(2*$r[3])." ms")."
64 octets recus de 8.8.8.8 : ".($r[4]>70?"perdu.":"temps ecoule=".(2*$r[4])." ms")."
5 paquets transmis, ".(5-$nb_perdus)." recus, ".($nb_perdus/5*100)."% perdus
rtt min/moy/max = ".strval($min*2)."/".strval(round($somme*2/(count($r)-$nb_perdus)))."/".strval($max*2)."ms", "l29mEEzIb3", '
import time

def envoyerICMP( destination ):
    """
    Envoie un paquet ICMP à un hôte distant et calcule le temps écoulé avant de recevoir la réponse.

    Paramètre :
    - destination : str, l\'adresse IP d\'un hôte distant.

    Retour : le temps écoulé entre l\'envoi et la réception de la réponse en millisecondes.

    Exceptions : lance l\'exception TimeoutError si un paquet envoyé n\'est jamais reçu.

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
    ainsi qu\'un résumé de l\'activité incluant le pourcentage de paquets perdus, le temps minimal, moyen et maximal sous la forme : 

    64 octets reçus de [destination] : temps écoulé=[x] ms

    [nb_paquets] paquets transmis, [n] recus, [p]% perdus
    rtt min/moy/max = [min]/[moy]/[max] ms

    où destination est l\'adresse IP de l\'hôte distant
       x est le nombre de millisecondes écoulées (arrondi à la ms près).
       min est le nombre minimal de millisecondes écoulées (arrondi à la ms près).
       moy est le nombre moyen de millisecondes écoulées (arrondi à la ms près).
       max est le nombre maximal de millisecondes écoulées (arrondi à la ms près).
       n est le nombre de paquets reçus.
       p est le pourcentage de paquets perdus (envoyés mais non reçus).
 
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
  Socket.no+=1
  if Socket.nbs[Socket.no-1]>70 :
      raise TimeoutError("Ping timed out after "+str(Socket.nbs[Socket.no-1]*2)+" ms")

  sleep(Socket.nbs[Socket.no-1]/1000)
  
def open_socket( x ):
 return Socket()
 
', "bravo.html");

?>
