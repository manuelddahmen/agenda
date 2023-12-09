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

//require_once "framework.php";
require_once "db.php";
require_once "initIdHospitalise.php";

initIdHospitalise();
global $db;
$db = new MyDB();

$jour__semaine_demie__heure_temps = '-1:-1:-1';
$jour__semaine_demie__heure_temps_0 = '0';
$jour__semaine_demie__heure_temps_1 = '08.30';
$jour__semaine_demie__heure_temps_2 = '1';
$jour__semaine_demie__heure_temps_0 = '-1';
$jour__semaine_demie__heure_temps_1 = '-1';
$jour__semaine_demie__heure_temps_2 = '-1';

$jour__semaine_demie__heure_temps_2 = isset($_GET["jour__semaine_demie__heure_temps_2"]) ? $_GET["jour__semaine_demie__heure_temps_2"] : $jour__semaine_demie__heure_temps_2;
$jour__semaine_demie__heure_temps_1 = isset($_GET["jour__semaine_demie__heure_temps_1"]) ? $_GET["jour__semaine_demie__heure_temps_1"] : $jour__semaine_demie__heure_temps_1;
$jour__semaine_demie__heure_temps_0 = isset($_GET["jour__semaine_demie__heure_temps_0"]) ? $_GET["jour__semaine_demie__heure_temps_0"] : $jour__semaine_demie__heure_temps_0;
$jour__semaine_demie__heure_temps = $jour__semaine_demie__heure_temps_0 . ":" . $jour__semaine_demie__heure_temps_1 . ":" . $jour__semaine_demie__heure_temps_2;

global $id_hospitalise;
if(is_scalar($id_hospitalise))
    $id_hospitalise = array();

$data["jour__semaine_demie__heure_temps"] = $jour__semaine_demie__heure_temps;

if (isset($_GET["id_employe"])) {
    $id_employe = urldecode($_GET["id_employe"]);
    $data["id_employe"] = $id_employe;
} else {
//    $id_employe = rand(0, PHP_INT_MAX);
}

if (isset($_GET["id_activite"])) {
    $id_activite = urldecode($_GET["id_activite"]);
} else {
    $id_activite = -1;
    $data["id_activite"] = $id_activite;
}
if (isset($_GET["id_tache"])) {
    $id_tache = urldecode($_GET["id_tache"]);
} else {
    $id_tache = -1;
    $data["id_tache"] = $id_tache;
}

if (isset($_GET["submit"])) {
    if (isset($id_activite)) {
        $activite = "update";
    } else {
        $activite = "insert";
    }
    if (isset($id_tache)) {
        $tache = "update";
    } else {
        $tache = "insert";
    }
} else if (isset($_GET["refresh"]) || (!isset($tache) || !isset($activite))) {
    $tache = "select";
    $activite = "select";
}
$id_tache = isset($id_tache) ? $id_tache : -1;
$id_employe = isset($id_employe) ? $id_employe : -1;
$nom_activite = isset($nom_activite) ? $nom_activite : -1;
$table_tache_get = array("id_activite" => $id_activite,
    "id_hospitalises" => $id_hospitalise,
    "id" => $id_tache,
    "jour__semaine_demie__heure_temps" => $jour__semaine_demie__heure_temps);
$table_activite_get = array("id" => $id_activite,
    "nom_activite" => isset($nom_activite) ?? "",
    "id_employe" => $id_employe
);
$message = "Edit ... ";
global $userData;
if ($userData == NULL) {
    echo "<h2>Non connecté</h2>";
    exit(0);
}
$sql = "select th.chambre as id_hospitalises, ta.id as id_activite, te.id as id_employe, tt.id as id_tache, ta.nom_activite as nomActivite, tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, * from table_hospitalises th inner join table_taches tt on th.chambre = tt.id_hospitalises inner join table_activites ta on ta.id = tt.id_activite inner join table_employes te on ta.id_employe = te.id
    where th.user_id=" . ($userData["id"]) . " and ta.user_id=" . ($userData["id"]) . " and tt.user_id=" . ($userData["id"]) . ";";

$stmt = $db->prepare($sql);
$stmt->execute();
$resultTacheActivite = $stmt->fetchAll();

$executed = "SELECT";

global $a, $m, $j;
//$id_activite = $resultTacheActivite[0]["id_activite"];

if (isset($_GET["id_tache"]) && $_GET["id_tache"] != null) {
    $id_tache = urldecode($_GET["id_tache"]);
    $existing_task = true;
    $data["id_tache"] = $id_tache;
}
try {
    if ($id_tache != -1 && isset($_GET["submit"])) {

        $sql = "update table_taches set id_activite=" . $id_activite . ", jour__semaine_demie__heure_temps='$jour__semaine_demie__heure_temps'
          where id=" . $id_tache . " and user_id=" . ($userData["id"]) . ";";;
        $stmt = $db->prepare($sql);
        $result = $stmt->execute();
        $message .= "Edit task succeeded (1/2)";
    } else if ($tache != "select" && $id_tache == -1 && isset($_GET["submit"])) {
        echo $_GET["submit"] . $id_tache;
        $sql = "insert into table_taches ( id, id_activite, jour__semaine_demie__heure_temps, user_id, id_hospitalises) values
                (" . rand(0, PHP_INT_MAX) . ", $id_activite, '$jour__semaine_demie__heure_temps', " . ($userData['id']) . ", 1);";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $id_tache = $db->handle->lastInsertId();
        $message .= "New task saved (1/2)";

    }
    // Mettre à jour la liste des patients
    if ($id_tache > 0 && isset($_GET["submit"])) {
        //if (isset($_GET["id_hospitalises"])) {
        $sql = "delete from table_taches_patients where id_tache=" . $id_tache . " and user_id=" . ($userData["id"]) . ";";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $id = $db->handle->lastInsertId() + 1;
        $countUpdatedAdd = 0;
        foreach ($_GET as $i => $chambre) {
            if (str_starts_with($i, "id_hospitalises_") || str_starts_with($i, "id_hospitalise")) {
                $id++;
                $sql = "insert into table_taches_patients  (id_patient, id_tache, user_id) values (" . $chambre . ", " . $id_tache . ", " . ($userData["id"]) . ");";
                $stmt = $db->prepare($sql);
                echo $stmt->execute();
                $countUpdatedAdd++;
            }
        }
        $message .= "Edit task succeeded (2/2)";
    }
} catch (Exception $exception) {
    print_r($exception);
    print_r($sql);
}


if ($id_tache > 0) { // New : existing tasks load.
    $sql = "select tt.id as id, tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps, tt.id_activite as id_activite, ttp.id_patient as id_patient from table_taches_patients as ttp inner join table_taches as tt on ttp.id_tache = tt.id 
         where tt.id=" . $id_tache . " and tt.user_id=" . ($userData["id"]) ." and ttp.user_id=" . ($userData["id"]) . ";";;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $resultPatientsTache = $stmt->fetchAll();
    foreach ($resultPatientsTache as $i => $rowItem) {
        if ($rowItem["id"] == $id_tache) {
            if(is_array($id_hospitalise)) {
              $id_hospitalise[] = $rowItem["id_patient"];
            } else {
              $id_hospitalise[] = $rowItem["id_patient"];
            }
            $jour__semaine_demie__heure_temps = $rowItem["jour__semaine_demie__heure_temps"];
            $arrayJD = explode(":", $jour__semaine_demie__heure_temps);
            $jour__semaine_demie__heure_temps_1 = $arrayJD[1];
            $jour__semaine_demie__heure_temps_0 = $arrayJD[0];
            $jour__semaine_demie__heure_temps_2 = $arrayJD[2];

            $id_activite = $rowItem["id_activite"];
        }
    }
}

global $j;
global $m;
global $a;
global $joursSemaine, $jours;
$jourSemaine = jourSemaine("$j-$m-$a");
//echo "<p>Quel jour est-ce ?" . ($joursSemaine[(int)($jourSemaine)]) . "</p>";
for ($i = 1; $i < 6; $i++) {
    if (strlen($jours[$i][1]) > 0 && $j > 0) {
        if ($j < $jours[1][1]) {
            //echo "Mois précédent";
        } else if ($j > $jours[5][1]) {
            //echo "Dernière semaine du mois $j " . $jours[5][1];
        } else if (($j - (int)($jours[$i][1])) >= 0 && ($j - (int)($jours[$i][1])) < 7) {
            //echo "<p>Le lundi de la semaine courante est " . ($jours[$i][1]) . "</p>";
        }
    } else {
    }
    //echo "$j lundi".$jours[$i][1];
}

global $page;

if(isset($_GET["notInclude"])) {
    $str="";
    if(is_array($id_hospitalise)) {
        foreach($id_hospitalise as $i => $id) {
            $str.="&id_hospitalise=".$id;
        }
    } else if(isset($id_hospitalise)) {
        $str.="&id_hospitalise=".$id;
    }
    $index = "index.php?page=agenda".$str;
    //header("Location: ."\n");
    echo "<script type='text/javascript'> window.location = '$index'; </script>";
    exit();
}
echo $jour__semaine_demie__heure_temps;
?>
<form action="index.php" method="get" onsubmit="return checkTache('save');"
      id="edition_activite" name="edition_activite">
    <input type="hidden" name="page" value="<?php echo "advent"; ?>">

    <?php

    if (isset($id_hospitalise)) { ?>
        <a class="button" href="index.php?page=agenda<?php echo implodeIdsInUrl("id_hospitalise", $id_hospitalise); ?>">Voir la semaine
            de -</a>
        <a class="button error"
           href="index.php?page=agenda&id_hospitalise=<?php echo implodeIdsInUrl("id_hospitalise", $id_hospitalise); ?>&table=table_taches&action=delete&id=<?php echo $id_tache; ?>&idName=id">Supprimer
            la tâche de <?php echo "$id_tache, ".(string)(is_array($id_hospitalise)?$id_hospitalise[0]:$id_hospitalise)."  "; ?></a>
        <?php
    }
    ?>
    <table id="edit_tache">
        <tr>
            <td>

                <label class="ligne2"
                       for="submit"
                       class="btn-submit"><?php
                    global $id_tache;
                    echo ($id_tache != -1) ? "Tache no" . $id_tache : "Nouvelle tâche"; ?></label>

                <?php echo ($id_tache == -1)
                    ? "Nouvelle tâche" : "Edition tâche" ?>
            <td>
                <input id="new_task" name="new_task" class="ligne2 btn-new" type="button"
                       onclick="newTaskButton()" value="Nouvelle tâche"/>
                <input id="id_tache" name="id_tache" class="ligne2 btn-new" type="hidden"
                       value="<?php echo $id_tache; ?>"/>

            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <?php
                global $userData;
                $stmt = $db->prepare("select * from table_hospitalises" . " where user_id=" . ($userData["id"]) . ";");
                $stmt->execute();
                $resultHospitalises = $stmt->fetchAll();

                $stmt = $db->prepare("select * from table_employes" . " where user_id=" . ($userData["id"]) . ";");
                $stmt->execute();
                $resultEmployes = $stmt->fetchAll();

                $stmt = $db->prepare("select * from table_activites" . " where user_id=" . ($userData["id"]) . ";");
                $stmt->execute();
                $resultActivites = $stmt->fetchAll();


                global $id_hospitalise;
                global $id_tache;
                global $id_activite;
                global $id_employe;

                $tablesIds = array(
                    "hospitalises" => $id_hospitalise,
                    "taches" => $id_tache,
                    "activites" => $id_activite,
                    "employes" => $id_employe
                );

                $i = 0;

                //print_r($tablesIds);
                ?>            </td>
        </tr>
        <tr>
            <td id="td_patient">Patient</td>
            <td>
                <?php
                //selectOptions("id_hospitalises", $resultHospitalises, "chambre", $id_hospitalise, array("nom", "prenom"), "refreshDataSemaineTaches()");

                global $id_hospitalise;
                global $resultPatientsTache;


                $sec = (is_array($resultPatientsTache) && isset($resultPatientsTache[0]["id_patient"])) ? $resultPatientsTache : $id_hospitalise;
                if(!is_array($sec)) {
                    $v = $sec;
                    $sec = array();
                    $sec[0]["id_patient"] = $v;
                }
                checkMultiple1("id_hospitalises_", $resultHospitalises, $sec, "chambre", array("nom", "prenom"), "refreshDataSemaineTaches()",
                    "chkbox(this)", $id_hospitalise);

                ?>
                <div>
                    <a class="btn-choose" href="#"
                       onclick="javascript:include2('<?php echo "ajax/request_form.php?page=pati&action=add&table=table_hospitalises&id=-1&idName=chambre"; ?>');"
                    >Ajouter un patient</a>
                    <a class="btn-choose"
                       href="<?php echo "?page=pati&action=manage&table=table_hospitalises&id=-1&idName=chambre"; ?>"
                       target="_blank">Gérer les patients</a>
                </div>
            </td>
        </tr>
        <tr>
            <td>Activité</td>
            <td>

                <?php

                echo $id_activite;
                selectOptions("id_activite", $resultActivites, "id", $id_activite, array("nom_activite"), "onchange=refreshDataSemaineTaches()");


                ?>
                <div>
                    <a class="btn-choose" href="#"
                       onclick="include2('<?php echo "ajax/request_form.php?page=acti&action=add&table=table_activites&id=-1&idName=id"; ?>');"
                    >Créer une activité</a>
                    <a class="btn-choose"
                       href="<?php echo "?page=acti&action=manage&table=table_activites&id=-1&idName=id"; ?>"
                       target="_blank">Gérer les activités</a>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                Jour
            </td>
            <td>
                <select name="jour__semaine_demie__heure_temps_0" id="jour__semaine_demie__heure_temps_0"
                        class="btn-choose ligne2" onchange=refreshDataSemaineTaches()>
                    <option draggable="true" value="-1">-------</option>
                    <?php
                    global $halfHour;
                    global $days;
                    foreach ($days as $key => $day) {
                        ?>
                        <option draggable="true" value="<?php echo $key; ?>"
                            <?php if ($jour__semaine_demie__heure_temps_0 == $key) {
                                echo " selected='selected' ";
                            } ?>>
                            <?php
                            echo $day;

                            ?>
                        </option>
                        <?php
                    }
                    ?>
                    <option draggable="true" value="7">Tous les jours</option>
                </select>
                <div>
                </div>
            </td>
        </tr>
        <tr>
            <td>Heure</td>
            <td>
                <select name="jour__semaine_demie__heure_temps_1" id="jour__semaine_demie__heure_temps_1"
                        class="btn-choose ligne2" onchange=refreshDataSemaineTaches()>
                    <option draggable="true" value="-1">-------</option>
                    <?php
                    foreach ($halfHour as $key => $hour) {
                        ?>
                        <option draggable="true" value="<?php echo $hour; ?>"
                            <?php if ($jour__semaine_demie__heure_temps_1 == $hour) {
                                echo " selected='selected' ";
                            } ?>>
                            <?php
                            echo $hour;
                            ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Durée</td>
            <td>
                <select name="jour__semaine_demie__heure_temps_2" id="jour__semaine_demie__heure_temps_2"
                        class="btn-choose ligne2" onchange=refreshDataSemaineTaches()>
                    <option draggable="true" value="-1">-------</option>
                    <?php
                    $times = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
                    foreach ($times as $key => $time) {
                        ?>
                        <option draggable="true" value="<?php echo $time; ?>"
                            <?php if ($jour__semaine_demie__heure_temps_2 == $time) {
                                echo " selected='selected' ";
                            } ?>>
                            <?php
                            echo "Durée: " . halfHourText($time);
                            ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="hidden" id="notInclude" name="notInclude"
                       value="notInclude"/>
                <input type="submit" id="submitTacheSave" onclick="return checkTache('save');" name="submit"
                       value="Enregistrer"/>
                <input type="submit" id="submitTacheSaveAndNew" onclick="return checkTache('saveAndNew');" name="submit"
                       value="Enregistrer et nouvelle tâche"/>

    </table>
    <p id="errors"></p>
</form>

<?php

echo $message;

$params = array();
if ($id_hospitalise != -1)
    $params["id_hospitalises"] = $id_hospitalise;
if ($id_activite != -1)
    $params["id_activite"] = $id_activite;
if ($id_tache != -1)
    $params["id_tache"] = $id_tache;
if ($id_employe != -1)
    $params["id_employe"] = $id_employe;
/*if($jour__semaine_demie__heure_temps_2!=null)
    $params['field-like'.$jour__semaine_demie__heure_temps] =$jour__semaine_demie__heure_temps_2;
if($jour__semaine_demie__heure_temps_2!=null)
    $params['field-like-'.$jour__semaine_demie__heure_temps] =$jour__semaine_demie__heure_temps_2;
if($jour__semaine_demie__heure_temps_2!=null)
    $params['field-like-'.$jour__semaine_demie__heure_temps] =$jour__semaine_demie__heure_temps_2;*/


addError("Notice", $sql);
//require_once "printTableWithGetters.php";

//printTableWithGetter($sql, $params, "id_tache");
/*
?>
<h1>table_taches</h1>
<?php
*/
global $id_hospitalise;

$action = $_GET["getvalues"] ?? "get";

$i = 0;

require_once "getdata_2.php";
$newGetData = new getdata_2($id_hospitalise??-1, $id_hospitalises??array());


$newGetData->init();

$result = joursTaches($id_hospitalise);

require_once "footer.php";
?>

</body>
</html>