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

require_once "db.php";
if($patient = isset($_GET["isFilterHospitalise"])) {
    $id_hospitalise = $_GET["id_hospitalise"];
}
if($activite = isset($_GET["isFilterActivite"])) {
    $id_activite = $_GET["id_activite"];
}
if($employe = isset($_GET["isFilterEmploye"])){
    $id_employe = $_GET["id_employe"];
}
$filters = array();
if(isset($id_hospitalise))
    $filters["hospitalise"] = $id_hospitalise;
if(isset($id_tache))
    $filters["activite"] = $id_activite;
if(isset($id_employe))
    $filters["employe"] = $id_employe;

global $db;
$sqlH = "select * from table_hospitalises;";
$sqlA = "select *, te.nom as nomEmploye from table_taches inner join table_activites on table_taches.id_activite = table_activites.id inner join table_employes te on table_activites.id_employe= te.id;";
$sqlE = "select * from table_employes;";


$stmtH = $db->prepare($sqlH);

$resultH = $stmtH->execute();

$resultH = $stmtH->fetchAll();

$stmtA = $db->prepare($sqlA);

$resultA = $stmtA->execute();

$resultA = $stmtA->fetchAll();

$stmtE = $db->prepare($sqlE);

$resultE = $stmtE->execute();

$resultE = $stmtE->fetchAll();




?><input type="checkbox" name="isFilterHospitalise"/><?php
?><input type="checkbox" name="isFilterActivite"/><?php
?><input type="checkbox" name="isFilterEmploye"/><?php
selectOptions("select_hospitalise", array(), "id", $id_hospitalise,
    array("nom", "prenom"));
selectOptions("select_activite", array(), "id", $id_activite,
    array("nom_activite"));
selectOptions("select_employe", array(), "id", $id_employe,
    array("nom", "prenom"));
?>
