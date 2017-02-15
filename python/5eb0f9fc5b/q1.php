<?php

require('../quiz.php');

execute("Question 1", "Exécutez la fonction <code>test</code>.", "Test réussi.", "b9Z7ng6bPi", '
def test():
    """
    Fonction de test.

    Affiche systématiquement les mots «Test réussi.»

    """
    print("Test réussi.")

');

?>
