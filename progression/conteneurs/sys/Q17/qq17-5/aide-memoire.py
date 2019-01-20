clé="bE8I.B+PEXQ+4qR"
with open("message_chiffré") as f:
    message=f.read()

with open("/tmp/reponse","w") as g:
    for i in range(len(message)):
        g.write(chr(ord(message[i])^ord(clé[i%len(clé)])))
    g.write("\n")

print("Votre message secret a été inscrit dans /tmp/reponse")
