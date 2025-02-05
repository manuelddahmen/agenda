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

global $db;
global $username;
global  $editAddDeleteStr;

if(!isset($username) || strlen($username)==0){
    $username = "anonyme";
}

global $userData;
if($userData==NULL) {
    echo "<h2>Non connecté</h2>";
}
if (isset($editAddDeleteStr)) {
    echo $editAddDeleteStr;
}
?>
<table id="table_tables">
    <tr><td >
    <h1>Liste des patients</h1>
<?php


global $action;
if(!isset($action)||$action!="edit") {

echo printTable("table_hospitalises", array(array('nom', 'prenom', 'sex', 'rehabilitation',
    'acti_obli_1', 'acti_obli_2'),
    array('Nom', 'Prénom', 'Sexe', 'Rehab', 'Activité 1', 'Activité 2')),
    array('varchar', 'varchar', 'varchar', 'integer', 'integer', 'integer'),
    array('chambre'), array('integer'), 'AddEditHospi',
    array("acti_obli_1"=>array("tablename"=>"table_activites", "references"=>"id",
          "display"=>array("nom_activite")),
          "acti_obli_2"=>array("tablename"=>"table_activites", "references"=>"id",
          "display"=>array("nom_activite"))),
    "index.php?page=tables", false,
    array(array("<a href='index.php?page=agenda&id_hospitalise=_id_'>Voir</a>", "_id_")));
?></td></tr><tr>
        <td ><h1>Equipe</h1>
<?php
echo printTable("table_employes", array('nom', 'prenom', 'fonction'),
    array('varchar', 'varchar', 'varchar', 'varchar'),
    array('id'), array('integer'),
    'AddEditHospi', null, "index.php?page=tables"
);

?></td></tr><tr>
        <td ><h1>Liste des activités</h1>
            <?php

echo printTable("table_activites", array(array('nom_activite', 'id_employe', 'autonomie'),
    array('Activité', 'Personnel', 'Autonomie')),
    array('varchar', 'datetime', 'datetime', 'integer'),
    array('id'), array('integer'),
    'AddEditHospi', array("id_employe"=>array("tablename"=>"table_employes", "references"=>"id",
        "display"=>array("nom", "prenom"))), "index.php?page=adacti"
);
            ?></td></tr><tr>
        <td >

        </td></tr><tr>
<?php
/*
?>

 <td ><h1>Table des patients en activités</h1>
<?php



echo printTable("table_taches", array('id_hospitalises','id_activite', "jour__semaine_demie__heure_temps"),
array('integer', 'integer', 'varchar', "int", "int", "int"),
array('id'), array('integer'),
'AddEditHospi',
array("id_activite"=>array("tablename"=>"table_activites", "references"=>"id",
 "display"=>array("nom_activite")), "id_hospitalises" => array("tablename"=>"table_hospitalises",
 "references"=>"chambre", "display"=>array("nom", "prenom")),
), null, "index.php,page=tables");
?></td></td></tr>
</table>
<?php
*/

}

require_once "footer.php";
?>
