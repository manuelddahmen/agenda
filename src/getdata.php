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

/*
 * Coche / décoche -> Mettre à jour la vue semaine.
Drag and Drop:
1) Drag sur activité: ajout à la tâche (icône 1)
2) Drag sur case vide : Création tâche
3) Drag de planning à poubelle : Drag tâche -> poubelle
4) Drag de planning à poubelle : Drag patient -> poubelle
   Effacer activité si activité vide avec confirmation.
 */

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
require_once "js_runphp_errors.php";
require_once "navigation.php";
require_once "db.php";
require_once "vue_agenda_date_mois.php";
require_once "printTableWithGetters.php";
require_once "logon.php";
require_once "login.php";
if (!checkLoginForm()) {
    error_log("No login => exit");
        exit();
}

global $username;
$username = $username ?? $_SESSION['username'];

//print_r($_GET);
global $id_hospitalise;
//print_r($id_hospitalise);
$id_hospitalise = array();
foreach ($_GET as $key => $value) {
    if (str_starts_with($key, "id_hospitalise_")) {
        $key = substr($key, strlen("id_hospitalise_"));
        $id_hospitalise[$key] = $value;
    }
}
//print_r($id_hospitalise);

class GetData
{

    private array|false $resultsPatient;
    public array|false $resultsPatients;
    private array $dates;
    private array $patients;
    private array $employes;

    function __construct($id_hospitalise)
    {
    }

    function init()
    {

        $dataTypes = array(
            "events" => array("table" => "table_activites", "id" => "id", "fields" => array(
                "name" => "nom_activite",
                "date_start" => "start_data")),
            "occurrence" => array("table" => "table_taches", "id" => "id", "fields" => array()),
            "perfomer" => array("table" => "table_employes", "id" => "id", "fields" => array()),
            "attendee" => array("table" => "table_hospitalises", "id" => "chambre", "fields" => array())
        );
        global $db, $id_hospitalise;
        $sql2 = "";
        if (is_array($id_hospitalise)) {
            $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, ";
            $sql2 = " where 1 and ( 0";
            foreach ($id_hospitalise as $key => $value) {
                $sql2 .= " or th.chambre=$id_hospitalise ";
            }
            $sql2 .= ") ";
        } else if ($id_hospitalise > 0) {
            $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, ";
            $sql2 .= " where th.chambre=$id_hospitalise ";
        } else { // Tous les patients
            $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, ";
            $sql2 .= " ";
        }

        if (count_chars($sql2) == 2)
            $sql2 = "";

        $sqlPatients = "select $sql1 chambre, nom, prenom "
            . "from table_hospitalises th join table_taches tt on th.chambre = tt.id_hospitalises inner join table_activites ta on ta.id = tt.id_activite "
            . "inner join table_taches_patients ttp on tt.id = ttp.id_tache and ttp.id_patient=th.chambre "
            . " union "
            . " select $sql1 chambre, nom, prenom    from table_hospitalises th join table_taches tt2 on th.chambre = tt2.id_hospitalises inner join table_activites ta on ta.id = tt2.id_activite "
            . $sql2;

//print_r($sql);

        if ($id_hospitalise > 0)
            $sqlPatient = "select * from table_hospitalises";
        else
            $sqlPatient = "select * from table_hospitalises;";

        addError("Notice", $sqlPatient);


        $stmt2 = $db->prepare($sqlPatients);

        $stmt2->execute();

        $resultPatients = $stmt2->fetchAll();


        $stmt = $db->prepare($sqlPatient);

        $stmt->execute();

        $result = $stmt->fetchAll();

        $this->resultsPatients = $resultPatients;
        $this->resultsPatient = $result;
    }

    function makeArray(): array
    {
        /**            foreach ( array(0, 1, 2 ,3 ,4 ,5 , 6) as $dayOdWeek) {
         * global  $halfHours;
         * ;foreach ($halfHours as $hourInDay) {
         * echo $array[$dayOdWeek][$hourInDay];
         *
         * }
         * }*/
        $this->dates = array();

        $this->retrieveData();

        $this->patients = array();
        $this->employes = array();

        return $this->dates;
    }

    function retrieveData($dates = null, $arrPatients = array(), $arrEmployes = array()): void
    {
    }

    function output(): bool
    {
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($this->resultsPatients, array($xml, 'addChild'));
        print $xml->asXML();
        return true;

    }

    public function retrievePlanning($action)
    {
        global $id_hospitalise;
        $array = joursTaches($id_hospitalise);
        return $array;
    }

    public function retrieveAllPatient(string $string)
    {
        global $db;
        $sql = "select *, chambre as id_hospitalise from table_hospitalises;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

global $db;
global $action;
global $datetime;

function joursTaches($id_hospitalise): array
{
    global $db;
    global $jour__semaine_demie__heure_temps, $id_employe, $id_hospitalise, $id_tache, $id_activite;
    global $datetime;
    $condition = "";
    $conditionTtp = "";

    if (is_array($id_hospitalise)) {
        $condition .= " and (0 ";
        $conditionTtp .= " and (0 ";
        $c = 0;
        foreach ($id_hospitalise as $value) {
            $condition .= " or th.chambre=$value ";
            $conditionTtp.= " or ttp2.id_patient=$value ";
            $c++;
        }
        if ($c == 0) {
            $condition .= " or 1 ) ";
            $conditionTtp .= " or 1 ) ";
        } else {
            $condition .= " ) ";
            $conditionTtp .= " ) ";
        }
    }
    $conditionA = "";
    if (isset($id_employe))
        $conditionA .= $id_employe != -1 ? " and te.id=$id_employe " : "";
    if (isset($id_tache))
        $conditionA .= $id_tache != -1 ? " and tt.id=$id_tache " : "";
    if (isset($id_activite))
        $conditionA .= $id_activite != -1 ? " and ta.id=$id_activite " : "";

    $condition = " where 1 " . $condition;
    $conditionTtp = " where 1 " . $conditionTtp;
    $sql2 = "";
    //print_r($activitiesCommunes);
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
    $condition.= $conditionA.$sql2;
    $conditionTtp.= $conditionA.$sql2;

    $sqlPatientsMultiple = "select ttp.id_tache as id_tache, te.id as id_employe, ta.id as id_activite, ttp.id_patient as chambre, 
       th.nom as nomPatient, th.prenom as prenomPatient, ta.nom_activite as nom_tache, 
       te.nom as nomEmploye, te.prenom as prenomEmploye,
         tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, 
         tt.id as id_tache, te.id as id_employe 
            from table_taches_patients ttp 
            inner join table_hospitalises th on th.chambre = ttp.id_patient
            inner join table_taches tt on tt.id = ttp.id_tache
            inner join table_taches_patients ttp2 on tt.id=ttp2.id_tache
            inner join table_activites ta on ta.id = tt.id_activite
            inner join table_employes te on ta.id_employe = te.id
            $conditionTtp                                                                                                                                            
            order by jour__semaine_demie__heure_temps asc ;";

    global $jour__semaine_demie__heure_temps_0, $jour__semaine_demie__heure_temps_1, $jour__semaine_demie__heure_temps_2;
    $stmt = $db->prepare($sqlPatientsMultiple);
    $stmt->execute();
    $resultPatientMultiples = $stmt->fetchAll();

    $activitiesCommunes = array();
    if ($resultPatientMultiples != null) {
        foreach ($resultPatientMultiples as $rowItem) {
            if (!isset($activitiesCommunes[$rowItem["id_tache"]])) {
                $activitiesCommunes[$rowItem["id_tache"]] = array();
            }
            $activitiesCommunes[$rowItem["id_tache"]][$rowItem["chambre"]] = $rowItem;
        }
    }


    $sql = "select tt.id as id_tache, te.id as id_employe, ta.id as id_activite, th.chambre as chambre, th.nom as nomPatient, th.prenom as prenomPatient, ta.nom_activite as nom_tache, te.nom as nomEmploye, te.prenom as prenomEmploye,
         tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, tt.id as id_tache,  te.id as id_employe
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



    $jours = traitementListPatient($result, $jours);
    $jours = traitementListPatients($resultPatientMultiples, $activitiesCommunes, $jours);


    $jours2 = array();

    return $jours;
}

function fusionnerResultatPatientPatients(array $jours1, array $jours2): array
{
    $jours = array();


    return $jours;
}

function traitementListPatients(array $result, array $activitiesCommunes, array $jours): array
{
    global $datetime, $id_hospitalise, $halfHour;
    if (!isset($jours))
        $jours = array();
    for ($j = 0; $j < 7; $j++) {
        global $datetime, $id_hospitalise, $halfHour;
        if (!isset($jours[$j]))
            $jours[$j] = array();
        foreach ($halfHour as $k => $halfHourLoopValue) {
            if (!isset($jours[$j][$halfHourLoopValue]))
                $jours[$j][$halfHourLoopValue] = "";
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
                    if ($position >= 0 && $length >= 0 && $position + $length < count($halfHour)) {
                        $endHour = $halfHour[(int)$position + (int)$length];
                    } else if ($position >= 0 && $length == 0) {
                        $endHour = $halfHour[count($halfHour) - 1];
                        $numHour = $halfHour[0];
                    } else {
                        $endHour = $halfHourLoopValue;
                    }
                    if ($numDayDbItem == 7) {
                        $numDayDbItem = $j;
                        //echo "Jour 7";
                    }

                    $dateDb = "$numDayDbItem:$numHour:$length";
                    $currentHourDb = "$numDayDbItem:$numHour";

                    if ($numHour != $halfHourLoopValue) {
                        continue 1;
                    }

                    $currentHour = "$j:";
                    global $halfHour;
                    $patients = "";

                    if (str_contains($currentHourDb, $currentHour) &&
                        ($keyHour = array_search($arrDate[1], $halfHour)) !== false) {
                        if (!isset($jours[$j][$halfHourLoopValue])) {
                            $jours[$j][$halfHourLoopValue] = "";
                        }

                        $id_tache1 = $value["id_tache"];
                        if (isset($activitiesCommunes[$id_tache1])) {
                            foreach ($activitiesCommunes[$id_tache1] as $id_patient => $array_patient_tache) {
                                if (is_array($id_hospitalise) && array_search($id_patient, $id_hospitalise)) {
                                    $patients .= "&nbsp;" . "<p><b><i>" . $array_patient_tache["prenomPatient"] . "&nbsp;" .
                                        $array_patient_tache["nomPatient"] . "</i></b></p>" . "&nbsp;";
                                } else {
                                    $patients .= "&nbsp;" . "<p><i>" . $array_patient_tache["prenomPatient"] . "&nbsp;" .
                                        $array_patient_tache["nomPatient"] . "</i></p>" . "&nbsp;";
                                }
                            }
                        }
                        if (strlen($patients) > 0 && !str_contains($jours[$j][$halfHourLoopValue], $patients)) {
                            $jours[$j][$halfHourLoopValue] .= "<h3>" . $numHour . "&nbsp;-&nbsp;" . $endHour . "</h3>";
                            $jours[$j][$halfHourLoopValue] .= "<p>" . $patients . "<b>" .
                                $value["nom_tache"] . "</b></p><p>" .
                                $value["prenomEmploye"] . " " .
                                $value["nomEmploye"] ."</p><p>" .listActivitiesHtml($value, true)
                                . "</p>";
                        }
                    }

                }

                //if
                //then
                // add data to jours[i]
                // sort data
                //else
                // continue
            }
        }
    }

    return $jours;
}

function traitementListPatient(array $result, array $jours): array
{
    global $datetime, $id_hospitalise, $halfHour;
    if (!isset($jours))
        $jours = array();
    for ($j = 0; $j < 7; $j++) {
        if (!isset($jours[$j]))
            $jours[$j] = array();
        foreach ($halfHour as $k => $halfHourLoopValue) {
            if (!isset($jours[$j][$halfHourLoopValue]))
                $jours[$j][$halfHourLoopValue] = "";
            foreach ($result as $index => $value) {
                $date = $value["jour__semaine_demie__heure_temps"];
                if ($date != NULL) {

                    $arrDate = explode(":", $date);
                    $numDayDbItem = $arrDate[0];
                    $numHour = $arrDate[1] >= 10 || count($arrDate) > 1 ? $arrDate[1] : "0" . $arrDate[1];
                    // Chercher heure dans $halfHour,
                    $length = $arrDate[2];
                    $position = array_search($numHour, $halfHour, true);
                    if ($position >= 0 && $length >= 0 && $position + $length < count($halfHour)) {
                        $endHour = $halfHour[(int)$position + (int)$length];
                    } else if ($position >= 0 && $length == 0) {
                        $endHour = $halfHour[count($halfHour) - 1];
                        $numHour = $halfHour[0];
                    } else {
                        $endHour = $halfHourLoopValue;
                    }
                    if ($numDayDbItem == 7) {
                        $numDayDbItem = $j;
                        //echo "Jour 7";
                    }

                    if ($numHour != $halfHourLoopValue) {
                        continue 1;
                    }
                    $dateDb = "$numDayDbItem:$numHour:$length";
                    $currentHourDb = "$numDayDbItem:$numHour";

                    $currentHour = "$j:";
                    global $halfHour;
                    $patients = "";
                    if (str_contains($currentHourDb, $currentHour) &&
                        ($keyHour = array_search($arrDate[1], $halfHour)) >= 0) {

                        $jours[$j][$halfHourLoopValue] .= "<h3>" . $numHour . "&nbsp;-&nbsp;" . ($endHour ?? $numHour) . "</h3>";
                        if (!isset($id_hospitalise) || (is_array($id_hospitalise) && array_search($value["chambre"], $id_hospitalise) >= 0)
                            || ((is_string($id_hospitalise) == 0 && strlen($id_hospitalise)) || ($value["chambre"] > 0))
                            || ((is_int($id_hospitalise) && $id_hospitalise == $value["chambre"]))) {
                            $patients .= "<p><i>" . $value["prenomPatient"] . " " . $value["nomPatient"] . "</i></p>";
                            $jours[$j][$halfHourLoopValue] .= "<p>" . $patients . "<b>" .
                                $value["nom_tache"] . "</b></p><p> " .
                                $value["prenomEmploye"] . " " .
                                $value["nomEmploye"] . listActivitiesHtml($value, true)
                                . "</p>";
                        }
                    }/* else {
                        $jours[$j][$halfHourLoopValue] .= "<h3>" . $numHour . "&nbsp;-&nbsp;" . $endHour??$numHour . "</h3>";

                    }*/


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
    }
    return $jours;
}


function ligneActivitesAPlusieurs() : String  {
    $str = "";
    return $str;
}
function ligneActivitesSeuls() : String {
    $str = "";
    return $str;
}

function listActivitiesHtml($rowItem, $isEvent = false): string
{
    global $currentHour;
    global $datetime;
    global $id_hospitalise;
    $str = "";
// Add: ajouter une tache tel jour à telle heure.
//    Modify-- Delete--
// Si tâche: modifier tel jour à telle heure telle tâche.
//    +Modify +Delete
    if (!$isEvent) {
        $url = addToGetUrl("?page=advent&id_tache=-1"."&datetime=$datetime", $rowItem);
        $str .= "<a href='$url' class='add'><img src='../images/add.png' alt='Add task'></a>";

    } else {
        $url = addToGetUrl("?page=advent&table=table_taches&idName=id&id=" . $rowItem["id_tache"] . "&datetime=$datetime", $rowItem);
        $str .= "<a href='$url' class='modify'><img src='../images/modify.png' alt='Modify task'></a>";
        $url = addToGetUrl("?page=agenda&id=".($rowItem["id_tache"])."&idName=id&table=table_taches&action=delete&&datetime=$datetime", $rowItem);
        $str .= "<a href=" . $url . " class='delete'><img src='../images/delete.png' alt='Delete task'/></a>";
        $str .='<add-to-calendar-button name="Calendar" description="Play with me!" startDate="'.$datetime.'" startTime="'.$datetime.'" endTime="17:45" timeZone="Europe/Brussels" location="World Wide Web" recurrence="weekly" recurrence_interval="1" options="\'Apple\',\'Google\',\'iCal\',\'Outlook.com\',\'Yahoo\'"></add-to-calendar-button>';
    }
    return $str;
}


function print_planning($result, $id_hospitalise): void
{
    echo "<button onclick='tableToExcel();'>Télécharger feuille de calcul</button>";
    echo "<table class='agenda' id='agenda'>";
    global $halfHour, $days;
    echo "<tr>";
    for ($i = -1; $i < 7; $i++) {
        if ($i == -1) {
            echo "<td class='hours'><h2>Heures</h2></td>";
        } else {
            $day = $days[$i];
            echo "<td><h2>$day</h2></td>";
        }
    }
    global $halfHour, $days;
    echo "</tr>";
    foreach ($halfHour as $key => $item) {
        $itemType = false;
        for ($numDay = 0; $numDay < 7; $numDay++) {
            if(isset($result[$numDay][$item]) && strlen( $result[$numDay][$item])>0) {
                $itemType = true;
            }
        }
        if(!$itemType) {
            continue;
        }
        echo "<tr>";
        for ($numDay = -1; $numDay < 7; $numDay++) {
            if ($numDay == -1) {
                ?>
                <td class="hours"><?php echo $item ?></td>
                <?php

            } else {
                $day = $days[$numDay];
                ?>
                <td class="half_hour" id="day_<?php echo "$numDay $item" ?>"><?php
                global $id_hospitalise;
                $value = array("id_tache" => -1, "jour__semaine_demie__heure_temps_0" => $numDay,
                    "jour__semaine_demie__heure_temps_1" => $item, "jour__semaine_demie__heure_temps_2" => 1,
                    "id_hospitalise" => $id_hospitalise);
                echo $result[$numDay][$item] ?? listActivitiesHtml($value, false);//$item;

                ?></td><?php
            }
        }
        echo "</tr>";
    }
    ?>

    </table>
    <?php
}

global $id_hospitalise;
$result = joursTaches($id_hospitalise);
print_planning($result, $id_hospitalise);

