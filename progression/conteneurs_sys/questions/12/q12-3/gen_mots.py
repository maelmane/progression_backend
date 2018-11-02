from random import randrange

nb_chaines=int(input("Nb chaînes"))

for n in range(nb_chaines):
        for i in range(randrange(3,20)):
                    print(chr(randrange(26)+97), end='')
        print()
                            

