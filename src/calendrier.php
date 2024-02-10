<?php
/*
 * Copyright (c) 2023-2024. Manuel Daniel Dahmen
 *
 *

 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

$date = date("d-m-Y");
$arrDate = explode("-", $date);

if(isset($_GET["m"])) {
    $a = $_GET['a'];
    $m = $_GET['m'];
    $j = $_GET['j'];
} else {
    $j = (int)$arrDate[0];
    $m = (int)$arrDate[1];
    $a = (int)$arrDate[2];
}
$mois = array();
 // nom des mois en francais
 $mois[1] = "Janvier";
 $mois[2] = "Février";
 $mois[3] = "Mars";
 $mois[4] = "Avril";
 $mois[5] = "Mai";
 $mois[6] = "Juin";
 $mois[7] = "Juillet";
 $mois[8] = "Août";
 $mois[9] = "Septembre";
 $mois[10] = "Octobre";
 $mois[11] = "Novembre";
 $mois[12] = "Décembre";
 $jours = array();
 // si un jour, mois, annee, n'est pas spécifié alors on récupère la date actuelle
 // on détermine a quel jour de la semaine correspond le premier jour du mois affiché


function premierDernierJours($m, $a)
{
/// nombre de jour dans chaque mois
    $nbjour = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    //DE 0 pour dimanche à 0 pour lundi
    $premierJour = (1 + ((int)(date('w', date_create("01-$m-$a")->getTimestamp())))) % 7;//jddayofweek(cal_to_jd($CAL_FRENCH, $m, 1, $a), 0);
    // si on est dans une année bissextile alors on ajoute un jour au mois de fevrier sinon un recupere le nombre de jour dans le mois
    if ($m == 2) {
        if ((($a % 4 == 0) && ($a % 100 != 0)) || ($a % 400 == 0)) {
            $dernierJour = 29;
        } else {
            $dernierJour = 28;
        }
    } else {
        $dernierJour = $nbjour[$m - 1];
    }
    $premierJour = (($premierJour) - 1);
    if ($premierJour == 0)
        $premierJour = 7;
    return array($premierJour, $dernierJour);
}

list($premierJour, $dernierJour) = premierDernierJours($m, $a);
list($premierJour0, $dernierJour0) = premierDernierJours(($m-1<1)?12:($m-1), ($m-1<1)?$a-1:($m-1));
list($premierJour2, $dernierJour2) = premierDernierJours(($m+1>12)?1:($m+1), ($m+1>12)?$a+1:($m+1));
 // on commence la boucle pour stoker les informations relatives a chaque jour de la semaine
 $compteur = 0;
 for ($ligne=1;$ligne<7;$ligne++) {
     for ($colone=1;$colone<8;$colone++) {
         if ($ligne == 1) {
            // si le jour de la semaine est plus petit que le jour de la semaine correspondant au premier jour du mois
            // alors on n'affiche rien
            // sinon on stocke le jour dans le tableau
            if ($colone < $premierJour) {
                $jours2[$ligne][$colone] = $dernierJour2-($premierJour-$colone)+1;
                $jours[$ligne][$colone] = "";
            } else {

               $compteur++;
               $jours[$ligne][$colone] = $compteur;
               if($colone==$premierJour) {
                   $premierJour1["ligne"] = $ligne;
                   $premierJour1["colonne"] = $colone;

               }
            }
         } else {
            $compteur++;
            // si le jour atteint est plus petit ke le nombre de jour dans le mois
            // alors on le stocke
            // sinon on affiche rien
            if ($compteur <= $dernierJour) {
               $jours[$ligne][$colone] = $compteur;
               if($compteur==$dernierJour) {
                   $dernierJour1["ligne"] = $ligne;
                   $dernierJour1["colonne"] = $colone;
               }
            } else if($compteur>$dernierJour){
               $jours2[$ligne][$colone] = $premierJour2+($compteur-$dernierJour)+1;
                $jours[$ligne][$colone] = "";
            }
         }
     }
 }
 // on calcule le mois et lannee, precedent et suivant
 $mois_precedent = $m - 1;
 $mois_suivant = $m + 1;
 $annee_precedente = $a;
 $annee_suivante = $a;
 if ($mois_precedent < 1) {
     $mois_precedent = 12;
     $annee_precedente--;
 }
 if ($mois_suivant > 12) {
     $mois_suivant = 1;
     $annee_suivante++;
 }

?>