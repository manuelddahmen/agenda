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

require_once "framework.php";
require_once "db.php";
if(isset($str)) {
    echo $str;
}

global $userData;
if($userData==NULL) {
    echo "<h2>Non connecté</h2>";
    exit(0);
}


echo printTable("table_hospitalises", array(array('nom', 'prenom', 'sex', 'rehabilitation',
    'acti_obli_1', 'acti_obli_2'),
    array('Nom', 'Prénom', 'Sexe', 'Rehab', 'Activité 1', 'Activité 2')),
    array('varchar', 'varchar', 'varchar', 'integer', 'integer', 'integer'),
    array('chambre'), array('integer'), 'AddEditHospi',
    array("acti_obli_1"=>array("tablename"=>"table_activites", "references"=>"id",
        "display"=>array("nom_activite")),
        "acti_obli_2"=>array("tablename"=>"table_activites", "references"=>"id",
            "display"=>array("nom_activite"))),
    "index.php?page=tables");
/*
echo printTable("table_hospitalises", array(array('nom', 'prenom', 'sex', 'rehabilitation'),
    array('Nom', 'Prénom', 'Sexe', 'Rehab')),
    array('varchar', 'varchar', 'varchar', 'integer'),
    array('chambre'), array('integer'), 'AddEditHospi', null, "index.php?page=pati"
);
*/
?>
