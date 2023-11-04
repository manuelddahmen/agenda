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


global $db;
$db = new MyDB();


global $username;
if(!isset($username) || strlen($username)==0){
    echo "<p>Non connecté</p>";
    echo "<a href='index.php'>Connectez-vous</a>";
    echo "<a href='index.php'>Inscrivez-vous</a>";
}

global $strContent;
echo $strContent;

if (isset($str)) {
    echo $str;
}

?>
<div id="app_description">
    <h2>Utilisation des données utilisateur</h2>
    <p>L'application agenda permet de créer un agenda sous forme d'une table en ajoutant des événements
    à partir d'activités.</p>
    <p>Agenda App collecte les données suivantes : email de service Google (si login avec Google), email fourni
    lors de la connexion/inscription ou email utilisateur. Les données restent sur le site de manière privée, et
    ne sont pas exploitées via des services commerciaux. </p>
    <p>Les services tiers tels Google Analytics collectent des informations générales telles que le nombre d'utilisateurs
    et le données de l'agent de service http(s) tel que le navigateur web.</p>
    <h2>Amélioration de l'expérience utilisateur</h2>
    <p>L'application offre un canvas pour la création d'agenda de type "Horaires de la semaine. Avec plusieurs participants,
    plusieurs personnes responsables et de multiples occurences des activités via des tâches (nom non à but limitatif)</p>
    <p><a href="?page=privacy_policy">Politique en matière de vie privée</a></p>

</div>
