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

require_once "vue_agenda_date_mois.php";
global $pages, $pagesNames;
global $username;
$pages = array(
    "?page=tables"=>"Tables",// patients, activités, autonomies
    //"?page=pati"=>"Patients",
    //"?page=memb"=>"Personnel",
    //"?page=acti" => "Ajouter une activité",
    "?page=agenda" => "Agenda patients",
    "?page=advent" => "Ajouter à l'agenda",//occurrence d'
    "?page=profile" => "Profil ($username)");

$pagesNames = array_keys($pages);


global $pagesNames;
global $page;
if (!isset($_GET["page"])) {
    $page = "home";
} else {
    $page = $_GET["page"];
}
$page_id = array_search("?page=" . $page, $pagesNames);
function displayNav(): void
{
    global $pages, $page_id, $pagesNames;

    global $currentPage, $username;

    $i = 0;
    ?>
    <?php
    $page_prev = $pagesNames[(($page_id - 1) + count($pages)) % count($pages)];
    $page_next = $pagesNames[(($page_id + 1) + count($pages)) % count($pages)];
    ?>
    <header>
        <div class='navbar-left'>
            <ul>
        <li class='sousmenu'><a href='<?php if (!empty($page_prev)) {
        echo $page_prev;
    } ?>'><img src="../images/previous.png" height="20px" width="20px" alt="Retour arrière"/></a></li>

            <?php
            foreach ($pages as $url => $name) {
                if ($i == $page_id) {
                    $class = "current";
                } else {
                    $class = "";
                }
                echo "<li class='sousmenu $class'><a href='" . make_link($url) . "'>$name</a></li>";
                $i++;
            }
            //    echo "<li><a class='sousmenu sousoumenu' style='float: right' href='".make_link("?")."'>Aujourd'hui:" .date("d/m/Y")."</a><br/>";
            /*global lobal $j, $m, $a;
            $timestamp = date_create("".($_GET['a']??$a)."-".($_GET['m']??$m)."-". ($_GET['j']??$j));

            if($timestamp!==false) {
                echo "<button style='float: right'>Date du calendrier: " . date("d/m/Y", $timestamp->getTimestamp()) . "</button><br/>";
                display_calendar($_GET['j'] ?? $j, $_GET['m'] ?? $m, $_GET['a'] ?? $a);
            }*/

            global $loginForm;
            //echo "<li class='sousoumenu sousmenu'><a href='?page=parametres'>".$loginForm.'</a></li>';

            require_once "main_menu.php";

            ?>
    <li class='sousmenu'><a href='<?php if (isset($page_next)) {
            echo $page_next;
        } ?>'><img src="../images/next.png" height="20px" width="20px" alt="Next: Edition des tâches"></a></li>
<?php                echo "<li class='sousmenu'><a href='" . make_link("?page=help") . "' target='_blank' ><img id='help_img' alt='Search helper' src='../images/help.png' /></a></li>";
?>
    </ul></div></header>
    <?php

}

$tables = array(
        "table_hospitalises" => array("acti_obli_1"=>array("tablename"=>"table_activites", "references"=>"id",
    "display"=>array("nom_activite")),
          "acti_obli_2"=>array("tablename"=>"table_activites", "references"=>"id",
    "display"=>array("nom_activite"))),

    "table_employes" => array(),

    "table_activites" => array("id_employe" => array("tablename" => "table_employes", "references" => "id",
        "display" => array("nom", "prenom"))),

    "table_taches" => array("id_activite" => array("tablename" => "table_activites", "references" => "id",
        "display" => array("nom_activite")), "id_hospitalises" => array("tablename" => "table_hospitalises",
        "references" => "chambre", "display" => array("nom", "prenom"))), "table_activites_autonomie" => array(),

    "table_users" => array()
);