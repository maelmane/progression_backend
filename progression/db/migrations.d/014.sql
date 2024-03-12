DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
CREATE PROCEDURE migration()
proc: BEGIN
    SET @version := (SELECT `version` FROM `version`);
    IF @version >= 14 THEN
        LEAVE proc;
    END IF;

    START TRANSACTION;

    -- Migration pour ajouter un utilisateur avec un profil de test
    INSERT INTO `user` (
        `username`,
        `courriel`,
        `etat`, 
        `role`,
        `preferences`,
        `date_inscription`,
        `nom`,
        `prenom`, 
        `nom_complet`,
        `pseudo`,
        `biographie`,
        `occupation`,
        `avatar`
    ) VALUES (
        'martine',
        'martine@gmail.com',
        'Actif',
        'Admin',
        'Préférences',
        UNIX_TIMESTAMP(NOW()),
        'Bouchard',
        'Martine',
        'Martine Bouchard',
        'Didine',
        'Enseignante depuis toujours ! Je suis passionnée de la programmation bas niveau',
        'Enseignant',
        'lien_avatar.jpg'
    );

    UPDATE `version` SET `version` = 14;
    COMMIT;

END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
