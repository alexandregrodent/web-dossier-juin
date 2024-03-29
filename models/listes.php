<?php

// Enonces
function getEnonces () {
    $db = dbConnect();
    return mysqli_query($db, "SELECT * FROM enonce;");
}

function getEnonceById ($id) {
    $db = dbConnect();
    return mysqli_query($db, "
        SELECT contenu FROM enonce 
        WHERE idEnonce LIKE ". $id .";
        ");
}

function deleteEnonceById ($id) {
    $db = dbConnect();
    return mysqli_query($db, "DELETE FROM `enonce` WHERE `enonce`.`idEnonce` = $id");
}

function updateEnonceById ($id, $content) {
    $db = dbConnect();
    return mysqli_query($db, "
        UPDATE enonce 
        SET `contenu` = '" . $content . "'
        WHERE `enonce`.`idEnonce` = $id;
        ");
}


// Champs
function getChamps () {
    $db = dbConnect();
    return mysqli_query($db, "SELECT * FROM champ");
}

function getChamp ($name) {
    $db = dbConnect();
    return mysqli_query($db, "
        SELECT * FROM champ 
        WHERE nom LIKE '". $name ."';
        ");
}

function deleteChamp ($name) {
    $db = dbConnect();
    return mysqli_query($db, "DELETE FROM `champ` WHERE `champ`.`nom` LIKE '". $name. "'");
}

function updateChamp ($name, $newName, $type, $options) {
    $db = dbConnect();
    return mysqli_query($db, "
        UPDATE champ 
        SET `nom` = '" . $newName . "',
        `typechamp` = '". $type ."',
        `parametres` = '" . $options . "'
        WHERE `champ`.`nom` LIKE '". $name. "';
        ");
}

// Autre
function randFloat ($min, $max, $step) {
    return (rand() % ((++$max - $min) / $step)) * $step + $min;
}

// Génération énoné
function getGeneratedFileId () {
    $directory = "../public/generated";
    // Récupère le nom de tous les fichiers du dossier mentionné (Dans un système unix (linux/macos), .. et . apparaissent et ne correspondent à aucun fichier)
    $scanned_directory = array_diff(scandir($directory), array('..', '.'));

    $min = 1;
    $max = 9999999999;

    $i = 0;
    do {
        $id = (string)rand($min, $max);
        $valid = 1;

        // Si l'intervalle d'id est déjà entièrement occupé alors retourne une erreur;
        // +1 pour compter le min dans l'intervalle
        if ($i >= ($max-$min +1)) return -1;

        foreach ($scanned_directory as $value) {
            $existedFileId = str_replace(".html", "", $value);
            // Vérifie si l'id existe déjà (dans ce cas, regénère un nouvel id)
            if ($id == $existedFileId) $valid = 0;
        }

        $i++;
    } while ($valid == 0);

    // Faire en sorte que l'id fasse le nombre de caractères que le max
    if (strlen($id) < strlen((string)$max)) {
        do {
            $id = "0". $id;
        } while (strlen($id) < strlen((string)$max));
    }

    return $id;
}