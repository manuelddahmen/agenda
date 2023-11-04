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
require_once "db.php";
require_once "functions.php";
global $datetime;

function joursTaches()
{
    global $db;
    global $jour__semaine_demie__heure_temps, $id_employe, $id_hospitalise, $id_tache, $id_activite;
    global $datetime;
    $condition = "";
    if (isset($id_hospitalise))
        $condition .= $id_hospitalise != -1 ? " and th.chambre=$id_hospitalise " : "";
    if (isset($id_employe))
        $condition .= $id_employe != -1 ? " and te.id=$id_employe " : "";
    if (isset($id_tache))
        $condition .= $id_tache != -1 ? " and tt.id=$id_tache " : "";
    if (isset($id_activite))
        $condition .= $id_activite != -1 ? " and ta.id=$id_activite " : "";

    $condition = " where 1 " . $condition;


    $sqlPatientsMultiple = "select ttp.id_tache as id_tache, te.id as id_employe, ta.id as id_activite, ttp.id_patient as chambre, th.nom as nomPatient, th.prenom as prenomPatient, ta.nom_activite as nom_tache, te.nom as nomEmploye, te.prenom as prenomEmploye,
         tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, tt.id as id_tache,  te.id as id_employe from table_taches_patients ttp inner join table_hospitalises th
           on th.chambre = ttp.id_patient inner join table_taches tt on ttp.id_tache=tt.id
             inner join table_activites ta on ta.id = tt.id_activite 
           inner join table_employes te on ta.id_employe = te.id 
           $condition                                                                                                                                            
           order by jour__semaine_demie__heure_temps asc ;";

    global $jour__semaine_demie__heure_temps_0, $jour__semaine_demie__heure_temps_1, $jour__semaine_demie__heure_temps_2;
    $stmt = $db->prepare($sqlPatientsMultiple);
    $stmt->execute();
    $resultPatientMultiples = $stmt->fetchAll();

    $activitesCommunes = array();
    if ($resultPatientMultiples != null) {
        foreach ($resultPatientMultiples as $i => $rowItem) {
            if (!isset($activitesCommunes[$rowItem["id_tache"]])) {
                $activitesCommunes[$rowItem["id_tache"]] = array();
            }
            $activitesCommunes[$rowItem["id_tache"]][$rowItem["chambre"]] = $rowItem;
        }
    }
    $sql2 = "";

    //print_r($activitesCommunes);
    if (isset($jour__semaine_demie__heure_temps_0) || isset($jour__semaine_demie__heure_temps_1) || isset($jour__semaine_demie__heure_temps_2)) {
        if ($jour__semaine_demie__heure_temps_0 == -1) {
            $sql2 .= " and jour__semaine_demie__heure_temps like '%:%:%'";
        } else {
            $sql2 .= " and jour__semaine_demie__heure_temps like '" . htmlspecialchars($jour__semaine_demie__heure_temps_0) . ":%'";
        }
        if ($jour__semaine_demie__heure_temps_1 == -1) {
            $sql2 .= " and jour__semaine_demie__heure_temps like '%:%:%'";
        } else {
            $sql2 .= " and jour__semaine_demie__heure_temps like '%:" . htmlspecialchars($jour__semaine_demie__heure_temps_1) . ":%'";
        }
        if ($jour__semaine_demie__heure_temps_2 == -1) {
            $sql2 .= " and jour__semaine_demie__heure_temps like '%:%:%'";
        } else {
            $sql2 .= " and jour__semaine_demie__heure_temps like '%:" . htmlspecialchars($jour__semaine_demie__heure_temps_2) . "'";
        }
    }
    $condition .= $sql2;


    $sql = "select tt.id as id_tache, te.id as id_employe, ta.id as id_activite, chambre, th.nom as nomPatient, th.prenom as prenomPatient, ta.nom_activite as nom_tache, te.nom as nomEmploye, te.prenom as prenomEmploye,
         tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, tt.id as id_tache, te.id as id_employe
         from table_hospitalises th
         inner join table_taches tt on th.chambre = tt.id_hospitalises
         inner join table_activites ta on tt.id_activite = ta.id
         inner join table_employes te on ta.id_employe = te.id
         $condition   
         order by jour__semaine_demie__heure_temps asc ";

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $result = $stmt->fetchAll();

    $jours = array();

    for ($j = 0; $j < 7; $j++) {
        $jours[$j] = array();
    }
    $jours = traitementListPatients($resultPatientMultiples, $activitesCommunes, $jours);
    $jours = traitementListPatient($result, $jours);

    return $jours;
}

function traitementListPatients(array $result, array $activitesCommunes, array $jours): array
{
    global $datetime, $id_hospitalise, $halfHour;
    for ($j = 0; $j < 7; $j++) {
        foreach ($result as $index => $value) {
            $date = $value["jour__semaine_demie__heure_temps"];
            if ($date != NULL) {

                $arrDate = explode(":", $date);
                $numDayDbItem = $arrDate[0];
                $numHour = $arrDate[1] >= 10 || count($arrDate) > 1 ? $arrDate[1] : "0" . $arrDate[1];
                // Chercher heure dans $halfHour,
                $length = $arrDate[2];
                //print_r($numHour);
                //print_r($halfHour);
                $position = array_search(needle: $numHour, haystack: $halfHour, strict: true);
                if ($position > 0) {
                    $endHour = $halfHour[(int)$position + (int)$length];
                } else if ($position == 0) {
                    $endHour = $halfHour[count($halfHour) - 1];
                    $numHour = $halfHour[0];
                }
                if ($numDayDbItem == 7) {
                    $numDayDbItem = $j;
                    //echo "Jour 7";
                }
                $dateDb = "$numDayDbItem:$numHour:$length";
                $currentHourDb = "$numDayDbItem:$numHour";

                $currentHour = "$j:";
                global $halfHour;
                if (str_contains($currentHourDb, $currentHour) &&
                    ($keyHour = array_search($arrDate[1], $halfHour)) !== false) {
                    if (!isset($jours[$j][$keyHour])) {
                        $jours[$j][$keyHour] = "";
                    }
                    $patients = "";

                    $id_tache1 = $value["id_tache"];
                    $patients = "";
                    if (isset($activitesCommunes[$id_tache1])) {
                        foreach ($activitesCommunes[$id_tache1] as $id_patient => $array_patient_tache) {
                            if ($id_hospitalise < 1 || ($id_patient == $id_hospitalise))
                                $patients .= "&nbsp;" . "<i>" . $array_patient_tache["prenomPatient"] . "&nbsp;" .
                                    $array_patient_tache["nomPatient"] . "</i>" . "&nbsp;";
                        }
                    } else {
                        continue 1;
                    }
                    $jours[$j][$keyHour] .= "<h3>" . $numHour . "&nbsp;-&nbsp;" . $endHour . "</h3>";
                    $jours[$j][$keyHour] .= "<span>" . $patients . "<b>" .
                        $value["nom_tache"] . "</b> " .
                        $value["prenomEmploye"] . " " .
                        $value["nomEmploye"] . listActivitiesHtml($value, true)
                        . "</span>";
                    if ($arrDate[0] != 7) {
                        unset($result[$index]);
                    } else {

                    }
                }


            } else {
                echo "Pas d'heure pour le rendez-vous";
                $added = false;
            }
            //if
            //then
            // add data to jours[i]
            // sort data
            //else
            // continue
        }
    }

    return $jours;
}

function traitementListPatient(array $result, array $jours): array
{
    global $datetime, $id_hospitalise;
    for ($j = 0; $j < 7; $j++) {
        foreach ($result as $index => $value) {
            $date = $value["jour__semaine_demie__heure_temps"];
            if ($date != NULL) {

                $arrDate = explode(":", $date);
                $numDayDbItem = $arrDate[0];
                $numHour = $arrDate[1] >= 10 || count($arrDate) > 1 ? $arrDate[1] : "0" . $arrDate[1];
                // Chercher heure dans $halfHour,
                $length = $arrDate[2];
                global $halfHour;
                $position = array_search($numHour, $halfHour, true);
                if ($position > 0 && ((int)$position + (int)$length)<count($halfHour)) {
                    $endHour = $halfHour[(int)$position + (int)$length];
                } else if ($position == 0) {
                    $endHour = $halfHour[count($halfHour) - 1];
                    $numHour = $halfHour[0];
                }
                if ($numDayDbItem == 7) {
                    $numDayDbItem = $j;
                    //echo "Jour 7";
                }
                $dateDb = "$numDayDbItem:$numHour:$length";
                $currentHourDb = "$numDayDbItem:$numHour";

                $currentHour = "$j:";
                global $halfHour;
                if (str_contains($currentHourDb, $currentHour) &&
                    ($keyHour = array_search($arrDate[1], $halfHour)) !== false) {
                    if (!isset($jours[$j][$keyHour])) {
                        $jours[$j][$keyHour] = "";
                    }
                    $jours[$j][$keyHour] .= "<h3>" . $numHour . "&nbsp;-&nbsp;" . $endHour . "</h3>";
                    if ($value["chambre"] > 0 && $id_hospitalise == $value["chambre"]) {
                        $patients = "<i>" . $value["prenomPatient"] . " " . $value["nomPatient"] . "</i>";
                        $jours[$j][$keyHour] .= "<span>" . $patients . "<b>" .
                            $value["nom_tache"] . "</b> " .
                            $value["prenomEmploye"] . " " .
                            $value["nomEmploye"] . listActivitiesHtml($value, true)
                            . "</span>";
                    }
                    if ($arrDate[0] != 7) {
                        unset($result[$index]);
                    } else {

                    }
                }


            } else {
                echo "Pas d'heure pour le rendez-vous";
                $added = false;
            }
            //if
            //then
            // add data to jours[i]
            // sort data
            //else
            // continue
        }
    }
    return $jours;
}


$result = joursTaches();

function listActivitiesHtml($rowItem, $isEvent = false)
{
    global $currentHour;
    global $datetime;
    global $id_hospitalise;
    $str = "";
// Add: ajouter une tache tel jour à telle heure.
//    Modify-- Delete--
// Si tâche: modifier tel jour à telle heure telle tâche.
//    +Modify +Delete
    if ($isEvent == false) {
        $url = addToGetUrl("?page=advent&id_tache=-1&datetime=$datetime", $rowItem);
        $str .= "<a href='$url' class='add'><img src='../images/add.png' alt='Add task'></a>";

    } else {
        $url = addToGetUrl("?page=advent&idName=id&id=" . $rowItem["id_tache"]
            . "&datetime=$datetime", $rowItem);
        $str .= "<a href='$url' class='delete'><img src='../images/modify.png' alt='Modify task'></a>";
        $url = addToGetUrl("?page=advent&idName=id&id=" . $rowItem["id_tache"] . "&table=table_taches&action=delete&datetime=$datetime", $rowItem);
        $str .= "<a href=" . $url . " class='delete'><img src='../images/delete.png' alt='Delete task'/></a>";
    }
    return $str;
}

function displayTableWeek(): void
{
    echo "<button onclick='tableToExcel();'>Excel</button>";
    echo "<table class='agenda' id='agenda'>";
    global $halfHour, $days;
//print_r($days);
//print_r($halfHour);
    echo "<tr>";
    for ($i = -1; $i < 7; $i++) {
        if ($i == -1) {
            echo "<td><h2>Heures</h2></td>";
        } else {
            $day = $days[$i];
            echo "<td><h2>$day</h2></td>";
        }
    }
    echo "</tr>";
    foreach ($halfHour as $key => $item) {
        echo "<tr>";
        for ($numDay = -1; $numDay < 7; $numDay++) {
            if ($numDay == -1) {
                ?>
                <td class="half_hour" id=""><?php echo $item ?></td>
                <?php

            } else {
                $day = $days[$numDay];
                ?>
                <td class="half_hour" id=""><?php
                global $id_hospitalise;
                $value = array("id_tache" => -1, "jour__semaine_demie__heure_temps_0" => $numDay,
                    "jour__semaine_demie__heure_temps_1" => $item, "jour__semaine_demie__heure_temps_2" => 1,
                    "id_hospitalise" => $id_hospitalise);
                echo $result[$numDay][$key] ?? listActivitiesHtml($value, false);//$item;

                ?></td><?php
            }
        }
        echo "</tr>";
    }
?>
</table><?php
}
displayTableWeek();
?>
