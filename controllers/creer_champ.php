<?php
session_start();
model("creation");

if (isset($_POST['submitChamp']) && !empty($_POST['submitChamp'])) {
    // Formulaire champs validé !

    if (isset($_POST['nomChamp']) && !empty($_POST['nomChamp']) && isset($_POST['typeChamp']) && !empty($_POST['typeChamp'])) {
        $_SESSION['nomChamp'] = strtolower($_POST['nomChamp']);
        $_SESSION['typeChamp'] = strtolower($_POST['typeChamp']);
    }
    $nomChamp = $_SESSION['nomChamp'];
    $typeChamp = $_SESSION['typeChamp'];

    $nomChamp = str_replace("##", "", $nomChamp);
    $nomChamp = str_replace(" ", "_", $nomChamp);

    if (strlen($nomChamp) <= 50) {
        $res = getChamp($nomChamp);
        if ($res) {
        if (mysqli_num_rows($res) == 0) {
                if ($typeChamp === "number") {
                    if (isset($_POST['nombreBorneInferieure']) && !empty($_POST['nombreBorneInferieure']) && isset($_POST['nombreBorneSupperieure']) && !empty($_POST['nombreBorneSupperieure']) && isset($_POST['nombrePas']) && !empty($_POST['nombrePas'])) {
                        $nombreBorneInferieure = $_POST['nombreBorneInferieure'];
                        $nombreBorneSupperieure = $_POST['nombreBorneSupperieure'];
                        $nombrePas = $_POST['nombrePas'];

                        if ($nombreBorneInferieure < $nombreBorneSupperieure) {
                            $array = [$nombreBorneInferieure, $nombreBorneSupperieure, $nombrePas];
                            $paramsChamp = serialize($array);
                        } else {
                            $_POST['error'] = "Borne suppérieur inférieure à la borne inférieure !";
                        }
                    }
                } elseif ($typeChamp === "image") {
                    if (isset($_POST['paramsChamp']) && !empty($_POST['paramsChamp'])) {
                        $paramsChamp = $_POST['paramsChamp'];
                        $paramsChamp = str_replace("'", "\'", $paramsChamp);
                        $array = explode("; ", $paramsChamp);
                        $imgNotExist = [];

                        $validation = true;
                        $imgDirectory = "../public/assets/uploads/img/";
                        foreach ($array as $key => $value) {
                            $check = getimagesize($imgDirectory . basename($value));
                            if ($check != true) {
                                array_push($imgNotExist, $value);
                                $validation = false;
                            }
                        }
                        if ($validation === true) {
                            $paramsChamp = serialize($array);
                        } else {
                            if (count($imgNotExist) > 1) {
                                $_POST['error'] = implode(", ", $imgNotExist) ." ne sont pas des images valides !";
                            } else {
                                $_POST['error'] = $imgNotExist[0] ." n'est pas une image valide !";
                            }
                            unset($paramsChamp);
                        }
                    }
                } elseif ($typeChamp === "text") {
                    if (isset($_POST['paramsChamp']) && !empty($_POST['paramsChamp'])) {
                        $paramsChamp = $_POST['paramsChamp'];
                        $paramsChamp = str_replace("'", "\'", $paramsChamp);
                        $array = explode("; ", $paramsChamp);
                        $paramsChamp = serialize($array);
                    }
                } elseif ($typeChamp === "date") {
                    if (isset($_POST['dateBorneInferieure']) && !empty($_POST['dateBorneInferieure']) && isset($_POST['dateBorneSupperieure']) && !empty($_POST['dateBorneSupperieure'])) {
                        if (strtotime($_POST['dateBorneInferieure']) < strtotime($_POST['dateBorneSupperieure'])) {
                            $array = [$_POST['dateBorneInferieure'], $_POST['dateBorneSupperieure']];
                            $paramsChamp = serialize($array);
                        } else {
                            $_POST['error'] = "Borne suppérieur inférieure à la borne inférieure !";
                        }
                    }
                } else {
                    $_POST['error'] = "Type de champs invalide !";
                    session_unset();
                }

                if (isset($paramsChamp) && !empty($paramsChamp)) {
                    if (strlen($paramsChamp) <= 1000) {
                        $res = createChamp($nomChamp, $typeChamp, $paramsChamp);
                        if ($res) {
                            $_POST['success'] = "Votre champ a bien été ajouté dans la base de données !";
                            session_unset();
                        } else {
                            $_POST['error'] = "Erreur lors de l'exécution de la requête !";
                        }
                    } else {
                        $_POST['error'] = "Paramètres de champ trop grand ! (max: 1000 caractères après sérialisation)";
                    }
                }
            } else {
                $_POST['error'] = "Un champ utilise déjà ce nom !";
                session_unset();
            }
        } else {
            $_POST['error'] = "Erreur lors de l'exécution de la requête !";
            session_unset();
        }

    } else {
        $_POST['error'] = "Nom de champ trop grand ! (max: 50 caractères)";
        session_unset();
    }
}

view("creer_champ");