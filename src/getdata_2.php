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
/*
foreach ($_GET as $key => $value) {
    if (str_starts_with($key, "id_hospitalise_")) {
        $key = substr($key, strlen("id_hospitalise_"));
        $id_hospitalise[] = $value;
    }
}
*/
//print_r($id_hospitalise);

class getdata_2
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
        /*
                $dataTypes = array(
                    "events" => array("table" => "table_activites", "id" => "id", "fields" => array(
                        "name" => "nom_activite",
                        "date_start" => "start_data")),
                    "occurrence" => array("table" => "table_taches", "id" => "id", "fields" => array()),
                    "perfomer" => array("table" => "table_employes", "id" => "id", "fields" => array()),
                    "attendee" => array("table" => "table_hospitalises", "id" => "chambre", "fields" => array())
                );*/
        global $db, $id_hospitalise;
        $sql2 = "";
        if (is_array($id_hospitalise)) {
            $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, th.birthdate, th.acti_obli_1, th.acti_obli_2,";
            $sql2 = " where 1 and ( 0";
            foreach ($id_hospitalise as $key => $value) {
                $sql2 .= " or th.chambre=$value ";
            }
            $sql2 .= ") ";
        } else if ($id_hospitalise > 0) {
            $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, th.birthdate, th.acti_obli_1, th.acti_obli_2,";
            $sql2 .= " where th.chambre=$id_hospitalise ";
        } else { // Tous les patients
            $sql1 = "th.nom as nomPatient, th.prenom as prenomPatient, th.birthdate, th.acti_obli_1, th.acti_obli_2,";
            $sql2 .= " ";
        }
        global $userData;
        $sql21 = "and 1";//= $sql2 . " and th.user_id=" . ($userData["id"]) . " and ta.user_id=" . ($userData["id"]) . " and tt.user_id=" . ($userData["id"]) . " and ttp.user_id=" . ($userData["id"]);
        $sql22 = "and 1"; //= $sql2 . " and th.user_id=" . ($userData["id"]) . " and ta.user_id=" . ($userData["id"]) . " and tt.user_id=" . ($userData["id"]) . " and ttp.user_id=" . ($userData["id"]);

        if (count_chars($sql2) == 2)
            $sql2 = "";
        $sqlPatients = "select $sql1 th.chambre as chambre, nom, prenom "
            . "from table_hospitalises th join table_taches tt on th.chambre = tt.id_hospitalises inner join table_activites ta on ta.id = tt.id_activite "
            . "inner join table_taches_patients ttp on tt.id = ttp.id_tache and ttp.id_patient=th.chambre " . $sql21 . " "
            . "and  th.user_id=" . ($userData["id"]) . " and tt.user_id=" . ($userData["id"]) . " " . " and ta.user_id=" . ($userData["id"]) . " "
            . " union "
            . " select $sql1 chambre, nom, prenom    from table_hospitalises th join table_taches tt2 on th.chambre = tt2.id_hospitalises inner join table_activites ta on ta.id = tt2.id_activite "
            . $sql22.";";

//print_r($sql);

        if ($id_hospitalise > 0)
            $sqlPatient = "select * from table_hospitalises where user_id=" . ($userData["id"]) . ";";
        else
            $sqlPatient = "select * from table_hospitalises where user_id=" . ($userData["id"]) . ";";

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
//        print $xml->asXML();
        return true;

    }

    public function retrievePlanning($action)
    {
        global $id_hospitalise;
        $array = joursTaches($id_hospitalise);
        return $array;
    }

    public function retrieveAllPatient(string $string): bool|array
    {
        global $db;
        global $userData;
        $sql = "select *, chambre as id_hospitalise from table_hospitalises where user_id=" . ($userData["id"]) . ";";
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
    global $newGetData;
    $condition = "";
    $conditionTtp = "";

    if (is_array($id_hospitalise)) {
        $condition .= " and (0 ";
        $conditionTtp .= " and (0 ";
        $c = 0;
        foreach ($id_hospitalise as $key => $value) {
            $condition .= " or th.chambre=$value ";
            $conditionTtp .= " or ttp2.id_patient=$value ";
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
    $condition .= $conditionA . $sql2;
    $conditionTtp .= $conditionA . $sql2;

    global $userData;
    $conditionTtp .= " and ttp.user_id=" . ($userData["id"]) . " and te.user_id=" . ($userData["id"]) . " and ta.user_id=" . ($userData["id"])
        . " and tt.user_id=" . ($userData["id"]) . " and ttp2.user_id=" . ($userData["id"]);


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

    $condition .= " and te.user_id=" . ($userData["id"]) . " and ta.user_id=" . ($userData["id"])
        . " and tt.user_id=" . ($userData["id"]) . " and tt.user_id=" . ($userData["id"])
        . " and th.user_id=" . ($userData["id"]);

    $sql = "select tt.id as id_tache, te.id as id_employe, ta.id as id_activite, th.chambre as chambre, th.nom as nomPatient, th.prenom as prenomPatient, ta.nom_activite as nom_tache, te.nom as nomEmploye, te.prenom as prenomEmploye,
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


    $arrayDetails = array();
    $jours = traitementListPatient($arrayDetails, $result, $jours);
    $jours = traitementListPatients($arrayDetails, $resultPatientMultiples, $activitiesCommunes, $jours);


    $jours2 = array();

    return array($jours, $arrayDetails);
}

function array_search_subarray($needle, array $array, $indexName): array
{
    $arrayRet1 = array();
    $arrayRet2 = array();
    foreach ($array as $index2 => $value) {
        foreach ($value as $index3 => $value2) {
            if ($index3 == $indexName && $value2 == $needle) {
                $arrayRet1[] = $index2;
                $arrayRet2[] = $index3;
            }
        }
    }
    return array($arrayRet1, $arrayRet2);
}


function traitementListPatients(array &$arrayDetails, array $result, array $activitiesCommunes, array $jours): array
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
                    if ($position >= 0 && $length >= 0 && ((int)$position) + ((int)$length) < count($halfHour)) {
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
                        $patients .= listeItemPatientsAgenda($arrayDetails, $jours, $j, $halfHourLoopValue, $value, $activitiesCommunes, $id_hospitalise, $patients, $numHour, $endHour);

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

/**
 * @param array $jours
 * @param int $j
 * @param mixed $halfHourLoopValue
 * @param mixed $value
 * @param array $activitiesCommunes
 * @param mixed $id_hospitalise
 * @param string $patients
 * @param mixed $numHour
 * @param mixed $endHour
 * @return string
 */
function listeItemPatientsAgenda(array &$arrayDetails, array &$jours, int $j, mixed $halfHourLoopValue, mixed $value, array $activitiesCommunes, mixed $id_hospitalise, string $patients, mixed $numHour, mixed $endHour): string
{

    $countHalfHour = isset($arrayDetails[$j][$halfHourLoopValue]) ? count($arrayDetails[$j][$numHour]) : 0;
    //$countPatient = isset($arrayDetails[$j][$numHour][$countHalfHour]) ? count($arrayDetails[$j][$halfHourLoopValue][$countHalfHour]) : 0;
    $countPatient = 0;
    if (!isset($arrayDetails[$j])) {
        $arrayDetails[$j] = array();
    }
    if (!isset($arrayDetails[$j][$halfHourLoopValue])) {
        $arrayDetails[$j][$halfHourLoopValue] = array();
    }
    if (!isset($arrayDetails[$j][$halfHourLoopValue][$countHalfHour])) {
        $arrayDetails[$j][$halfHourLoopValue][$countHalfHour] = array();
    }


    if (!isset($jours[$j][$halfHourLoopValue])) {
        $jours[$j][$halfHourLoopValue] = "";
    }

    $id_tache1 = $value["id_tache"];
    if (isset($activitiesCommunes[$id_tache1])) {
        //echo "<p>";
        foreach ($activitiesCommunes[$id_tache1] as $id_patient => $array_patient_tache) {
            if (is_array($id_hospitalise) && array_search($id_patient, $id_hospitalise)) {
                $patients .= "&nbsp;" . "<b><i>" . $array_patient_tache["prenomPatient"] . "&nbsp;" .
                    $array_patient_tache["nomPatient"] . "</i></b><br/>s" . "&nbsp;";
                $arrayDetails[$j][$halfHourLoopValue]["chambre"][$countPatient] = $id_patient;

            } else {
                $patients .= "&nbsp;" . "<i>" . $array_patient_tache["prenomPatient"] . "&nbsp;" .
                    $array_patient_tache["nomPatient"] . "</i><br/>" . "&nbsp;";
                $arrayDetails[$j][$halfHourLoopValue]["chambre"][$countPatient] = $id_patient;
            }
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$countPatient]["chambre"] = $value["chambre"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$countPatient]["id_patient"] = $value["chambre"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$countPatient]["nomPatient"] = $value["nomPatient"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$countPatient]["prenomPatient"] = $value["prenomPatient"];
            //$arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$countPatient]["dateDb"] = $endHour;

            $countPatient++;
        }
        //echo "</p>";
    }
    if (strlen($patients) > 0/* && !str_contains($jours[$j][$halfHourLoopValue], $patients)*/) {
        $jours[$j][$halfHourLoopValue] .= "<h3>" . $numHour . "&nbsp;-&nbsp;" . $endHour . "</h3>";
        $jours[$j][$halfHourLoopValue] .= "<p>" . $patients . "<b>" .
            $value["nom_tache"] . "</b></p><p>" .
            $value["prenomEmploye"] . " " .
            $value["nomEmploye"] . "</p><p>" . listActivitiesHtml($value, true)
            . "</p>";

        foreach ($arrayDetails[$j][$halfHourLoopValue][$countHalfHour] as $keyPatient => $value2) {
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$keyPatient]["nom_tache"] = $value["nom_tache"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$keyPatient]["id_employe"] = $value["id_employe"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$keyPatient]["id_tache"] = $value["id_tache"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$keyPatient]["prenomEmploye"] = $value["prenomEmploye"];
            $arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$keyPatient]["nomEmploye"] = $value["nomEmploye"];
            //$arrayDetails[$j][$halfHourLoopValue][$countHalfHour][$countPatient]["dateDb"] = $endHour;
        }

    }
    $countHalfHour++;


    return $patients;
}

function traitementListPatient(array &$arrayDetails, array $result, array $jours): array
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


                        $jours[$j][$halfHourLoopValue] =
                            listeItemPatientAgenda($arrayDetails, $halfHourLoopValue, $endHour, $jours[$j][$halfHourLoopValue], $j, $id_hospitalise, $value, $patients);
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
    }
    return $jours;
}

/**
 * @param mixed $numHourValue
 * @param mixed $endHour
 * @param $jours
 * @param mixed $id_hospitalise
 * @param mixed $value
 * @param string $patients
 * @return string
 */
function listeItemPatientAgenda(array &$arrayDetails, mixed $numHourValue, mixed $endHour, &$jours, $j, mixed $id_hospitalise, mixed $value, string $patients): string
{
    $countHalfHour = isset($arrayDetails[$j][$numHourValue]) ? count($arrayDetails[$j][$numHourValue]) : 0;
    $countPatient = isset($arrayDetails[$j][$numHourValue]["chambre"]) ? count($arrayDetails[$j][$numHourValue]["chambre"]) : 0;

    if (!isset($arrayDetails[$j])) {
        $arrayDetails[$j] = array();
    }
    if (!isset($arrayDetails[$j][$numHourValue])) {
        $arrayDetails[$j][$numHourValue] = array();
    }
    if (!isset($arrayDetails[$j][$numHourValue][$countHalfHour])) {
        $arrayDetails[$j][$numHourValue][$countHalfHour] = array();
    }

    $jours .= "<h3>" . $numHourValue . "&nbsp;-&nbsp;" . ($endHour ?? $numHourValue) . "</h3>";
    if (!isset($id_hospitalise) || (is_array($id_hospitalise) && array_search($value["chambre"], $id_hospitalise) >= 0)
        || ((is_string($id_hospitalise) == 0 && strlen($id_hospitalise)) || ($value["chambre"] > 0))
        || ((is_int($id_hospitalise) && $id_hospitalise == $value["chambre"]))) {
        $patients .= "<p><i>" . $value["prenomPatient"] . " " . $value["nomPatient"] . "</i></p>";
        $jours .= "<p>" . $patients . "<b>" .
            $value["nom_tache"] . "</b></p><p> " .
            $value["prenomEmploye"] . " " .
            $value["nomEmploye"] . listActivitiesHtml($value, true)
            . "</p>";
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["chambre"] = $value["chambre"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["id_patient"] = $value["chambre"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["id_employe"] = $value["id_employe"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["id_tache"] = $value["id_tache"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["nomPatient"] = $value["nomPatient"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["prenomPatient"] = $value["prenomPatient"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["nom_tache"] = $value["nom_tache"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["prenomEmploye"] = $value["prenomEmploye"];
        $arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["nomEmploye"] = $value["nomEmploye"];
        //$arrayDetails[$j][$numHourValue][$countHalfHour][$countPatient]["dateDb"] = $endHour;
    }
    $countHalfHour++;

    return $jours;
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
    if (is_array($rowItem) && isset($rowItem["dateDb"])) {
        $dateParts = explode(":", $rowItem["dateDb"]);
        $str .= halfHourText($dateParts[2]);
    }
    $string_hospi = "";
    if(isset($id_hospitalise) && is_array($id_hospitalise) && !$isEvent) {
        $string_hospi = implodeIdsInUrl("id_hospitalise",$id_hospitalise);
    } else if(isset($id_hospitalise) && is_numeric($id_hospitalise) && !$isEvent) {
        $string_hospi .= "&id_hospitalise=".$id_hospitalise;
    }
    if (!$isEvent) {
        $url = addToGetUrl("?page=advent&id_tache=-1" . "&datetime=$datetime".$string_hospi, $rowItem);
        $str .= "<a href='$url'  class='add'><img src='../images/add.png' alt='Add task'></a>";//onclick='//javascript:chkboxViewTache(\"$url\")'

    } else {
        $url = addToGetUrl("?page=advent&table=table_taches&idName=id&id=" . $rowItem["id_tache"] . "&datetime=$datetime".$string_hospi, $rowItem);
        $str .= "<a href='$url'   class='modify'><img src='../images/modify.png' alt='Modify task'></a>";//onclick='//javascript:chkboxViewTache(\"$url\")'
        $url = addToGetUrl("?page=agenda&id=" . ($rowItem["id_tache"]) . "&idName=id&table=table_taches&action=delete&&datetime=$datetime".$string_hospi, $rowItem);
        $str .= "<a href='$url'  class='delete'><img src='../images/delete.png' alt='Delete task'/></a>";
        //$str .='<add-to-calendar-button name="Calendar" description="Play with me!" startDate="'.$datetime.'" startTime="'.$datetime.'" endTime="17:45" timeZone="Europe/Brussels" location="World Wide Web" recurrence="weekly" recurrence_interval="1" options="\'Apple\',\'Google\',\'iCal\',\'Outlook.com\',\'Yahoo\'"></add-to-calendar-button>';
        $str .= "<a href='#'  class='notification ' onclick='eventNotification('"
            . ($rowItem["nom_activite"] ?? "")
            . "',"
            . ($rowItem["halfHourText"] ?? "")
            . ","
            . ($rowItem["hour"] ?? "")
            . ", "
            . ($rowItem["minutes"] ?? "")
            . ")' ><img src='../images/alarm.jpg' alt='Delete task'/></a>";
        //$str .='<add-to-calendar-button name="Calendar" description="Play with me!" startDate="'.$datetime.'" startTime="'.$datetime.'" endTime="17:45" timeZone="Europe/Brussels" location="World Wide Web" recurrence="weekly" recurrence_interval="1" options="\'Apple\',\'Google\',\'iCal\',\'Outlook.com\',\'Yahoo\'"></add-to-calendar-button>';
    }
    return $str;
}

/***
 * @param $result
 * @param $id_hospitalise
 * @return void
 */
function print_planning($result, $id_hospitalise): void
{
    echo "<button onclick='tableToExcel();'>Télécharger feuille de calcul</button>";
    echo "<table class='agenda' id='agenda1'>";
    global $halfHour, $days;
    echo "<tr>";
    for ($i = -1; $i < 7; $i++) {
        if ($i == -1) {
            echo "<td class='hours'><h2>h</h2></td>";
        } else {
            $day = $days[$i];
            echo "<td><h2>$day".listActivitiesHtml(null)."</h2></td>";
        }
    }
    global $halfHour, $days;
    echo "</tr>";
    foreach ($halfHour as $key => $item) {
        $itemType = false;
        for ($numDay = 0; $numDay < 7; $numDay++) {
            if (isset($result[$numDay][$item]) && strlen($result[$numDay][$item]) > 0) {
                $itemType = true;
            }
        }
        if (!$itemType) {
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

/***
 * @param $result
 * [day][halfHour][idxTache][id_tache]
 * [day][halfHour][idxTache][id_hospitalises]
 * [day][halfHour][idxTache][id_employe]
 * [day][halfHour][idxTache][nomEmploye]
 * [day][halfHour][idxTache][prenomEmploye]
 * [day][halfHour][idxTache][id_patient]
 * [day][halfHour][idxTache][nomPatient]
 * [day][halfHour][idxTache][prenomPatient]
 *
 * @param $id_hospitalise
 * @return void
 */
function print_planning2($result, $id_hospitalise): void
{
    global $newGetData;
    checkMultiple("id_hospitalise", $newGetData->retrieveAllPatient("get"),
        $newGetData->resultPatientsTache ?? array(), "chambre", array("nom", "prenom"),
        "onchange=refreshDataSemaineTaches()", "chkbox(this)");
    //echo "<button onclick='tableToExcel();'>Télécharger feuille de calcul</button>";
    //echo "<table class='agenda' id='agenda'>";
    global $halfHour, $days;
    echo "<tr>";
    for ($i = -1; $i < 7; $i++) {
        if ($i == -1) {
            echo "<td class='hours'><h2>h</h2></td>";
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
            if (isset($result[$numDay][$item]) && strlen($result[$numDay][$item]) > 0) {
                $itemType = true;
            }
        }
        if (!$itemType) {
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

                $last_task = -1;
                ?>
                <td class="half_hour" id="day_<?php echo "$numDay $item" ?>"><?php
                foreach ($result[$numDay][$item] as $tache_key => $value) {
                    global $id_hospitalise;
                    $value = array("id_tache" => $result[$numDay][$item]["id_tache"], "jour__semaine_demie__heure_temps_0" => $numDay,
                        "jour__semaine_demie__heure_temps_1" => $item, "jour__semaine_demie__heure_temps_2" => 1,
                        "id_hospitalise" => $id_hospitalise);
                    echo "<b>" . $result[$numDay][$item] . "</b>";
                    if ($result[$numDay][$item]["id_tache"] != $last_task) {
                        echo "<b>" . $value["nom_activite"] . "</b>";
                        echo "<span>" . $value["nomEmploye"] . " " . $value["prenomEmploye"] . "</span>";
                        echo listActivitiesHtml($value, false);//$item;
                    }
                    echo "<i>" . $value["nomPatient"] . " " . $value["prenomPatient"] . "</i>";
                }
                ?></td><?php
            }
        }
        echo "</tr>";
    }
    ?>

    </table>
    <?php
}


/***
 * @param array $result
 * @param array $arrayResultDetails
 * [day][halfHour][idxTache][numPatient][id_tache]
 * [day][halfHour][idxTache][numPatient][id_hospitalise]
 * [day][halfHour][idxTache][numPatient][id_employe]
 * [day][halfHour][idxTache][numPatient][id_patient]
 * [day][halfHour][idxTache][numPatient][nomEmploye]
 * [day][halfHour][idxTache][numPatient][prenomEmploye]
 * [day][halfHour][idxTache][numPatient][nomPatient]
 * [day][halfHour][idxTache][numPatient][prenomPatient]
 * @return array
 */
function fusionnerResultatPatientPatients(array &$arrayResultDetails): array
{
    $jours = array();

    global $halfHour;
    for ($j = 0; $j < 7; $j++) {
        $jours[$j] = array();
        foreach ($halfHour as $numHour => $halfHourValue) {
            $indicesUtilises = array();
            if (isset($arrayResultDetails[$j]) && is_array($arrayResultDetails[$j])
                && isset($arrayResultDetails[$j][$halfHourValue]) && is_array($arrayResultDetails[$j][$halfHourValue])
                && isset($arrayResultDetails[$j][$halfHourValue][0][0]["id_tache"])
                && is_array($arrayResultDetails[$j][$halfHourValue][0][0]["id_tache"])) {
                foreach ($arrayResultDetails[$j][$halfHourValue] as $i => $tache) {
                    if (count($arrayResultDetails[$j][$halfHourValue][$i]) > 1) {
                        list($indices1, $index2)
                            = array_search_subarray($arrayResultDetails[$j][$halfHourValue][$i][0],
                            $arrayResultDetails[$j][$halfHourValue], "id_tache");
                    } else if (count($arrayResultDetails[$j][$halfHourValue][$i]) > 0) {
                        $indices = array(array($i), array(0));
                    } else {
                        continue;
                    }
                    if (array_search($arrayResultDetails[$j][$halfHourValue][$i][0]["id_tache"], $indicesUtilises) >= 0) {

                    } else {
                        $jours[$j][$numHour][$i] = $tache;
                        for ($k = 0; $k < count($indices[0]); $k) {
                            $indicesUtilises[] = $arrayResultDetails[$j][$halfHourValue][$i][0]["id_tache"];
                        }
                    }
                }
            }
        }
    }
    return $jours;
}

$joursVaisselle = array();
$acti_obli = array();
function listePatients($id_hospitalise): void
{
    $list = "";
    //global $id_hospitalise;
    global $joursVaisselle;
    global $acti_obli;
    $getData = new getdata_2($id_hospitalise);
    $patients = $getData->retrieveAllPatient("get");
    echo "<tr><td colspan='8' class='titre_agenda'>";
    if(is_array($id_hospitalise)) {
        foreach ($id_hospitalise as $i => $chambre) {
            foreach ($patients as $patient) {
                if ($chambre == $patient["id_hospitalise"] && $chambre > 0) {
                    $list .= "-" . ($patient["nom"]) . " " . ($patient["prenom"]) . "- " . (date_locale_fr($patient["birthdate"])) . "-";
                    if (isset($patient) && isset($patient["vaisselle"]) && isset($patient["nom"])) {
                        $joursVaisselle[$patient["vaisselle"]] = $joursVaisselle[$patient["vaisselle"]] ?? "";
                        $joursVaisselle[$patient["vaisselle"]] .= ($patient["nom"]) . " " . ($patient["prenom"]);

                        $acti_obli["acti_obli_1"] = $patient["acti_obli_1"];
                        $acti_obli["acti_obli_2"] = $patient["acti_obli_2"];
                    }
                }
            }
        }
    }
    ?><h3>Agenda MSP Waremme</h3><?php
    echo "<h2 class='title_patient'>" . $list . "</h2>";
    echo "</td></tr>";
}

function fusionnerResultatPatientPatients2(array &$arrayResultDetails): void
{
    global $halfHour, $days;
    global $halfHour, $days, $id_hospitalise;

    echo "<table id='agenda' class='agenda'>";//THE GOOG

    listePatients($id_hospitalise);
    echo "<tr>";
    for ($i = -1; $i < 7; $i++) {
        if ($i == -1) {
            echo "<td class='hours'><h2>h</h2></td>";
        } else {
            $day = $days[$i];
            echo "<td><h2>$day</h2></td>";
        }
    }
    echo "</tr>";
    echo "<tr>";


    global $joursVaisselle;
    for ($i = -1; $i < 7; $i++) {
        if ($i == -1) {
            echo "<td></td>";
        } else {
            echo "<td class='btn-new'>" .
                ((isset($joursVaisselle[$i])) ?($joursVaisselle[$i]." (Vaisselle) ") :" ") .
                "</td>";
        }
    }
    echo "</tr>";
    foreach ($halfHour as $numHour => $halfHourValue) {
        echo "<tr>";
        $jEmpty = 0;
        for ($j = -1; $j < 7; $j++) {
            if (!isset($arrayResultDetails[$j][$halfHourValue]) || count($arrayResultDetails[$j][$halfHourValue]) == 0) {
                $jEmpty++;
            }
        }
        if ($jEmpty == 8)
            ;//continue;
        for ($j = -1; $j < 7; $j++) {
            if ($j == -1) {
                echo "<td class='hours half_hour notempty'>$halfHourValue</td>";
            } else {
                if (isset($arrayResultDetails[$j][$halfHourValue])) { ?>
                    <td class="half_hour empty" id="day_<?php echo "$j $halfHourValue" ?>"
                        onmouseenter="clickZoomEvent(this);"><?php
                    layoutCell($arrayResultDetails[$j][$halfHourValue]);

                    echo listActivitiesHtml(array("jour__semaine_demie__heure_temps" => "$j:$halfHourValue:1",
                        "jour__semaine_demie__heure_temps_0" => "$j",
                        "jour__semaine_demie__heure_temps_1" => "$halfHourValue",
                        "jour__semaine_demie__heure_temps_2" => "1",
                        //"dateDb"=>$arrayResultDetails[$j][$halfHourValue]["dateDb"],
                        "id_hospitalise" => $id_hospitalise[0] ?? 0), false);

                    ?></td><?php
                } else {
                    ?>
                    <td class="half_hour notempty" onmouseenter="/*clickZoomEvent(this);*/"><?php

                    echo listActivitiesHtml(array("jour__semaine_demie__heure_temps" => "$j:$halfHourValue:1",
                        "jour__semaine_demie__heure_temps_0" => "$j",
                        "jour__semaine_demie__heure_temps_1" => "$halfHourValue",
                        "jour__semaine_demie__heure_temps_2" => "1",
                        "id_hospitalise" => $id_hospitalise[0] ?? 0), false);

                    ?></td><?php
                }
            }
        }
        echo "</tr>";
    }

    ?>
    <tr><td>Activités obligatoires</td>
        <?php

        global $acti_obli;
        global $newGetData;
        global $db;
        foreach ($acti_obli as $value) {
            ?><td>
            <?php

            $sql = "select nom_activite from table_taches as tt inner join table_hospitalises as th "
                ." on th.chambre=tt.id_hospitalises inner join table_activites as ta "
                ." on tt.id_activite=ta.id where id_activite=".((int)($value)).";";

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if($result!=null && $result[0]["nom_activite"]!=null) {
                echo $result[0]["nom_activite"];
            }
            ?>
            </td>
        <?php } ?>
    </tr>
    </table>
    <?php


}


function layoutCell($dataHalfHour)
{
    $layout = "";

    global $id_hospitalise;

    //echo "<h3>Step 1</h3>";
    //print_r($dataHalfHour);

    $sortedData = array();

    foreach ($dataHalfHour as $key1 => $value1) {
        foreach ($value1 as $key2 => $value2) {

            // A peu près...
            if (!isset($dataHalfHour[0][0]["id_tache"])) {
                echo "Erreur tableau";
                print_r($dataHalfHour);
                return "";
            }
            if (isset($dataHalfHour[$key1][$key2]["id_tache"]) &&
                !isset($sortedData[$dataHalfHour[$key1][$key2]["id_tache"]])) {
                $sortedData[$dataHalfHour[$key1][$key2]["id_tache"]] = array();
            }
            //
            if (!isset($dataHalfHour[$key1][$key2]["chambre"])) {
                //print_r($dataHalfHour[0]);
            }
            if (isset($dataHalfHour[$key1][$key2]["id_tache"]) && isset($dataHalfHour[$key1][$key2]["chambre"])
                && ((is_scalar($id_hospitalise) && $dataHalfHour[$key1][$key2]["chambre"] == $id_hospitalise) ||
                    (is_array($id_hospitalise) &&
                        array_search(
                            $dataHalfHour[$key1][$key2]["chambre"],
                            $id_hospitalise) !== false))) {
                $sortedData[$dataHalfHour[$key1][$key2]["id_tache"]][$dataHalfHour[$key1][$key2]["chambre"]] = $value2;
            }


        }

    }
    $first = true;

    foreach ($sortedData as $id_tache => $value) {
        foreach ($value as $data) {
            if ($first) {
                $first = false;
                $id_tache_0 = -1;
            }
            if ($id_tache_0 == $id_tache && (is_array($id_hospitalise) && count($id_hospitalise) > 1)) {
                // Rajoute patient à tâche (sous-case cellule)
                // Rajoute patient à tâche (sous-case cellule)
//                $layout .= "<i>" . $data["id_patient"] . "</i>&nbsp;-&nbsp;";
                $layout .= "<span class='" . $data["id_patient"] . "'>" . $data["nomPatient"] . " " . $data["prenomPatient"] . "</span>";

            } else {
                // Nouvelle tâche (sous-case cellule) -> nom_activite, nomEmploye, prenomEmploye
                $layout .= "<div id='event_cell' class='tache event_cell " . $data["id_tache"] . "'>";
                if (isset($data["nom_tache"]) && (isset($data["nomEmploye"]) || isset($data["prenomEmploye"]))) {
                    $layout .= "<b>" . ($data["nom_tache"]) . "</b>&nbsp;-&nbsp;";

                    //$layout .= "<br/><span class='id_employe'>" . $data["nomEmploye"] . " " . $data["prenomEmploye"] . "</span>";
                }
                // Rajoute patient à tâche (sous-case cellule)
//                $layout .= "<i>" . $data["id_patient"] . "</i>&nbsp;-&nbsp;";
                if (is_array($id_hospitalise) && count($id_hospitalise) > 1) {
                    $layout .= "<span class='" . $data["id_patient"] . "'>" . $data["nomPatient"] . " " . $data["prenomPatient"] . "</span>";
                }
            }
            $id_tache_0 = $id_tache;
            $layout .= listActivitiesHtml($data, true);

            echo $layout;

            $layout = "";
        }
    }
    return $layout;
}

global $id_hospitalise;
$resultA = joursTaches($id_hospitalise);
$result = $resultA[0];
$arrayResult = $resultA[1];

fusionnerResultatPatientPatients2($arrayResult);
//print_planning2($jours, $id_hospitalise);

?>