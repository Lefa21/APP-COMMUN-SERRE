<?php
// helpers/conversion_helper.php

/**
 * Convertit une valeur brute d'humidité du sol en pourcentage.
 * Gère le fait que la valeur diminue lorsque l'humidité augmente.
 *
 * @param float $valeurBrute La valeur lue depuis le capteur.
 * @param int $valeurSeche La valeur de calibration pour un sol sec.
 * @param int $valeurHumide La valeur de calibration pour un sol saturé d'eau.
 * @return int Le pourcentage d'humidité (de 0 à 100).
 */
function convertirHumiditeEnPourcentage($valeurBrute, $valeurSeche = 3150, $valeurHumide = 1250) {
    // S'assure que la valeur brute est bien dans les limites calibrées
    $valeurBrute = max($valeurHumide, min($valeurSeche, $valeurBrute));
    
    // Calcule la plage de valeurs possibles
    $plage = $valeurSeche - $valeurHumide;
    if ($plage == 0) return 0; // Évite la division par zéro

    // Inverse la valeur pour que "plus humide" soit un pourcentage plus élevé
    $valeurInversee = $valeurSeche - $valeurBrute;
    
    // Calcule le pourcentage
    $pourcentage = ($valeurInversee / $plage) * 100;
    
    return round($pourcentage);
}