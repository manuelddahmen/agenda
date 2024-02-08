<?php
/*
 * Copyright (c) 2023. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
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

require_once "framework.php";
    global $datetime;
    ?>
    <?php

if(isset($_GET["id_hospitalise"])) {
    $id_hospitalise = ((int)urldecode($_GET["id_hospitalise"])) > 0 ? ((int)urldecode($_GET["id_hospitalise"])) : 0;
} else {
    $id_hospitalise = 0;
}


if($id_hospitalise>0) {
    $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, ";
    $sql2 = " where th.chambre=$id_hospitalise ";
}else { // Tous les patients
    $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, ";
    $sql2 = " ";
}
    $sql = "select tt.id as id_tache, te.id as id_employe, ta.id as id_activite, chambre, ". $sql1 ." ta.nom_activite as nom_tache, te.nom as nomAnim, te.prenom as prenomAnim,
       tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, tt.id as id_tache
from table_hospitalises th
         inner join table_taches tt on th.chambre = tt.id_hospitalises
         inner join table_activites ta on tt.id_activite = ta.id
         inner join table_employes te on ta.id_employe = te.id
         $sql2
         order by jour__semaine_demie__heure_temps asc ";

    //print_r($sql);

if($id_hospitalise>0)
    $sqlPatient = "select * from table_hospitalises";
else
    $sqlPatient  = "select * from table_hospitalises;";

addError("Notice", $sqlPatient);

global $db;

$stmt2 = $db->prepare($sqlPatient);

$resultPatients = $stmt2->execute();

$resultPatients = $stmt2->fetchAll();


    $stmt = $db->prepare($sql);

    $result = $stmt->execute();

    $result = $stmt->fetchAll();

    //print_r($result);

selectOptions("formSelectPatient", $resultPatients, "chambre", $id_hospitalise, array("nom", "prenom"),
   "refreshDataVueSemaine(this);");
checkMultiple("id_hospitalises", $resultPatients, $resultPatientsTache ?? array(),
    "chambre", array("nom", "prenom"), "onchange=refreshDataSemaineTaches()", "chkbox(this);");
function oldTable() {

    $idxResult = 0;
    function listActivitiesHtml0($rowItem = null)
    {
        global $currentHour;
        global $datetime;
        global $id_hospitalise;

// Add: ajouter une tache tel jour à telle heure.
//    Modify-- Delete--
// Si tâche: modifier tel jour à telle heure telle tâche.
//    +Modify +Delete
        if ($rowItem == null) {
            $url = addToGetUrl("?page=agenda&idName=id&id=-1&table=table_taches&action=edit&datetime=$datetime", $rowItem);
            echo "<a href='$url' class='delete'><img src=../images/add.png alt='Add'/></a>";

        } else {
            $url = addToGetUrl("?page=agenda&idName=id&id=".$rowItem["id_tache"]
            ."&table=table_taches&action=edit&datetime=$datetime", $rowItem);
            echo "<a href='$url"; ?>' class='delete'><img
                    src="../images/modify.png"/ alt='Modify'></a><?php
            $url = addToGetUrl("?page=agenda&idName=id&id=".$rowItem["id_tache"].
                "&table=table_taches&action=agenda&datetime=$datetime", $rowItem);
            echo "<a href='$url' class='delete'><img src='../images/delete.png' alt='Delete'/></a>";
        }
    }

    ?>

    <table><tr><td><a href='<?php echo make_link("?page=agenda");?>'><img src="../images/previous.png" alt=" Retour arrière"></a></td>
        <td><a href='<?php echo make_link("?page=agenda");?>"'><img src="../images/next.png" alt=" Onglet agenda"></a></td></tr></table><?php
    echo "<table class='agenda'><tr>";
    global $days, $halfHour;
    foreach ($days as $numDay => $nameDay) {
    ?>
    <td class="day_week" id="<?php echo $nameDay; ?>">
        <h2><?php echo $nameDay; ?></h2>
        <?php
        if(isset($halfHour[$idxResult]))
            $dbTimeEventStartString = "" . ($numDay) . ":" . $halfHour[$idxResult];
        foreach ($halfHour as $halfHourItem) {

                ?>            <h3><?php

                    echo $halfHourItem;
                    ?></h3>
                <?php
            if ($idxResult >= count($result)) {
                $added = false;
            }
            else {
                $added = true;
                while ($added) {
                    //echo "Plus d'évenement cette semaine";
                    if ($idxResult >= count($result)) {
                        break;
                    }

                    $date = $result[$idxResult]["jour__semaine_demie__heure_temps"];
                    //echo $date;
                    if ($date != NULL) {

                        $arrDate = explode(":", $date);
                        $numDayDbItem = $arrDate[0];
                        $numHour = $arrDate[1] >= 10 || count($arrDate) > 1 ? $arrDate[1] : "0" . $arrDate[1];
                        $length = $arrDate[2];
                        $dateDb = "$numDayDbItem:$numHour:$length";
                        $currentHourDb = "$numDayDbItem:$numHour";

                        $currentHour = "$numDay:$halfHourItem";

                        if (str_contains($currentHour, $currentHourDb)) {
                            //$dateDb Activité/RDV (durée: " . ($length / 2) . "h )
                            echo "<span><i>".$result[$idxResult]["prenomPatient"]." ".$result[$idxResult]["nomPatient"]."</i><b>" .
                                $result[$idxResult]["nom_tache"] . "</b> " .
                                $result[$idxResult]["prenomAnim"] . " " .
                                $result[$idxResult]["nomAnim"]
                                . "</span>";
                            $id_hospitalise = $result[$idxResult]["chambre"];
                            $added = true;
                            listActivitiesHtml0($result[$idxResult]);
                            $idxResult++;
                        } else {
                            $added = false;
                            /*$idxResult ++;
                            echo "Date invalide : format: \$numJour:\$HH.MM:\$numHalfHours <br>LOOP ITEM: $currentHour";
                            echo "<br>$currentHourDb";*/
                        }

                        $id_hospitalise = rand(0, PHP_INT_MAX);

                        if($added==false){
                            listActivitiesHtml0();
                        }


                    } else {
                        echo "Pas d'heure pour le rendez-vous";
                        $added = false;
                        $idxResult++;
                    }
                }

            }
        }
        ?>
    </td>
    <?php
    }
    ?></tr>
    </table>
<?php

    }
require_once "semaine.php";

require_once "footer.php";
?>  </body>
    </html>
<?php
?>