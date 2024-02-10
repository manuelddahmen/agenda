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

require_once "calendrier.php";
require_once "make_link.php";
function display_calendar($jp, $mp, $ap)
{
    global $j, $m, $a;
    [$jNow, $mNow, $aNow] = explode("-", date("d-m-Y"));
    [$jCurrent, $mCurrent, $aCurrent] = array($j, $m, $a);

    global $jours, $jours2, $mois, $mois_precedent, $mois_suivant, $annee_precedente, $annee_suivante;

    echo "<table class='calendrier' style='display: none;'>";
    echo "<tr><td><b>Lu</b></td><td ><b>Ma</b></td><td><b>Me</b></td><td><b>Je</b></td><td><b>Ve</b></td><td><b>Sa</b></td><td><b>Di</b></td></tr>";
    for ($ligne = 1; $ligne < 7; $ligne++) {
        echo "<tr>";
        for ($colonne = 1; $colonne < 8; $colonne++) {
            $j2 = $jours[$ligne][$colonne];
            echo "<td align='center' class='";
            if($j2=="") {
                echo "date-out-of-month";
            } else {
                if (($ap == $aNow) && ($mp == $mNow) && ($j2 == $jNow)) {
                    echo "date-now ";
                }
                if (($ap == $aCurrent) && ($mp == $mCurrent) && ($j2 == $jCurrent)) {
                    echo "date-current";
                }
            }
            echo "'>";
            if($j2=="") {
                echo "<a draggable='true' href='#'>" . $jours2[$ligne][$colonne] . "</a>";
            } else {
                $href = make_link("?m=" . $mp . "&a=" . $ap . "&j=$j2");
                echo "<a draggable='true' href='".$href."' >" . $jours[$ligne][$colonne] . "</a>";

            }
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "<tr><td><a href='?m=" . $mois_precedent . "&a=" . $annee_precedente . "&j=$jp'><<</a></td><td colspan='5'>" . $mois[$mp] . " " . $ap . "</td><td align='center'><a href='?m=" . $mois_suivant . "&a=" . $annee_suivante . "&j=$jp'>>></a></td></tr>";
    echo "</table>";
}
