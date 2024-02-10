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

require_once "../help.md";
$sql = "select * from table_hospitalises;";
global $db;
$stmt = $db->prepare($sql);
$stmt->execute();

$hospitalises = $stmt->fetchAll();


$sql = "select * from table_employes;";
global $db;
$stmt = $db->prepare($sql);
$stmt->execute();

$employes = $stmt->fetchAll();

$sql = "select tt.id, ta.nom_activite, te.nom as nom_employe, te.prenom as prenom_employe, th.nom as nom_patient, th.prenom as prenom_patient, tt.jour__semaine_demie__heure_temps as jour__semaine_demie__heure_temps from table_taches tt inner join table_activites ta on tt.id_activite = ta.id inner join table_hospitalises th on tt.id_hospitalises = th.chambre inner join table_employes te on ta.id_employe = te.id;";
global $db;
$stmt = $db->prepare($sql);
$stmt->execute();

$taches = $stmt->fetchAll();

$sql = "select ta.id, ta.nom_activite, te.nom, te.prenom from table_activites ta inner join table_employes te on te.id=ta.id_employe;";
global $db, $datetime;
$stmt = $db->prepare($sql);
$stmt->execute();

$activites = $stmt->fetchAll();

$text_note = $_GET["text_note"]??"";
$text_hospitalises = $_GET["hospitalises"]??"";
$text_activites = $_GET["activites"]??"";
$text_taches = $_GET["taches"]??"";
$text_employes = $_GET["employes"]??"";
$id = $_GET["id"]??-1;

$text_note = htmlentities($text_note);

if($id==-1) {
    $id = rand(1, 100000);
    global $datetime;
    global $username;
    $sql = "insert into table_notes(date_now, id, text_note, nom_utilisateur) values('$datetime', ".$id.", '$text_note', '$username')";
} else if($id>0) {
   $sql = "update table_notes set text_note='$text_note' where  id=$id";
}
if($sql!=null) {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    echo $sql;
}
$text_note = html_entity_decode($text_note);

$words  = explode("\n", $text_note);


// Ex. HttpRequest §dbUpdate.php?tablename=&action= ... avec output: Code http 200 400
// Il suffit d'ajouter un champ à l'url captable dans le if action==update|delete|saveNew ++ select afficher le formulaire
?>
<form id="edit_note" class="edit_note" action="search_menu.php" method="get">
    <p id="list" onchange="changeListUpdateButtons();" ondrop="">
        <span id="hospitalises" onclick="clearSpan('hospitalises');"></span>
        <span id="activites" onclick="clearSpan('activites');"></span>
        <span id="taches" onclick="clearSpan('taches');"></span>
        <span id="employes" onclick="clearSpan('employes');"></span>
        <span id="jour__semaine_demie__heure_temps_0" onclick="clearSpan('jour__semaine_demie__heure_temps_0');"></span>
        <span id="jour__semaine_demie__heure_temps_1" onclick="clearSpan('jour__semaine_demie__heure_temps_1');"></span>
        <span id="jour__semaine_demie__heure_temps_2" onclick="clearSpan('jour__semaine_demie__heure_temps_2');"></span>
        <span id="note_text" ></span>
        <textarea cols="80" rows="4" name="text_note"><?php
            echo $text_hospitalises." ";
            echo $text_activites ." ";
            echo $text_taches ." ";
            echo $text_employes ." ";
            echo $text_note ." ";
            ?></textarea>
        <input type="hidden" name="id" value="<?php echo $id; ?>"
        <?php if($id==-1) {
            ?>
            <input type="hidden" name="action" value="saveNew"/>
            <?php
        } else {
            ?>
        <input type="hidden" name="action" value="save"/>
        <?php
        }?>
        <input type="submit" name="submit" value="save"/>
    </p>
</form>
    <input type="text" id="mySearch" onkeyup="myFunctionSearchMenu()" placeholder="Search.." title="Type in a category">
    <table id="searchTerms">
    <tr>
        <td>
            <ul id="myMenu">
                <li class="hospitalises">
                    <a id="hospitalise-1" class="hospitalises"
                       href="?page=tables&action=add&table=table_hospitalises&id=-1&idName=chambre"
                       onclick="mySearchFunctionChooseTheme('hospitalises', -1)">Nouveau patient</a>
                </li>

                <li class="employes"><a href="<?php echo make_link("?page=tables&action=add&table=table_employes&id=-1&idName=id"); ?>" id="employe-1" class="employes"  onclick="mySearchFunctionChooseTheme('employes', -1)">Nouvel employé</a></li>
                <li class="taches"><a
                            href="?page=advent&id_tache=-1"

                            id="tache-1" class="taches" onclick="mySearchFunctionChooseTheme('taches',-1)">Nouvelle tâche</a></li>
                <li class="activites"><a
                            href="?page=tables&action=add&table=table_activites&id=-1&idName=id"
                            id="activite-1" class="activites"  onclick="mySearchFunctionChooseTheme('activites', -1);">Nouvelle activité</a></li>


                <?php
                foreach ($hospitalises as $item) {
                    ?>
                    <li class="hospitalises">
                        <a id="hospitalise<?php echo $item["chambre"]; ?>" class="hospitalises"
                           href="#"
                           onclick="mySearchFunctionChooseTheme('hospitalise<?php echo $item["chambre"] . "', " . $item["chambre"]; ?>)"><?php
                            echo $item["nom"] . " " . $item["prenom"]."</a></li>";
                            ?>
                   &nbsp;<a href="<?php echo make_link("?page=tables&action=edit&table=table_hospitalises&id=".$item["chambre"]); ?>&idName=chambre">Edit</a>
                &nbsp;<a href="<?php echo make_link("?page=tables&action=delete&table=table_hospitalises&id=".$item["chambre"]); ?> ?>&idName=chambre">Delete</a></li>
<?php
}

                foreach ($employes as $item) {
                    ?>
                    <li class="employes">
                        <a id="employe<?php echo $item["id"]; ?>" class="employes"
                           href="#"
                           onclick="mySearchFunctionChooseTheme('employe<?php echo $item["id"] . "', " . $item["id"]; ?>)"><?php
                            echo $item["nom"] . " " . $item["prenom"]."</a>&nbsp;</li>";

                            ?>
                   &nbsp;<a href="<?php echo make_link("?page=tables&action=edit&table=table_employes&id=".$item["id"]); ?>&idName=id">Edit</a>
                &nbsp;<a href="<?php echo make_link("?page=tables&action=delete&table=table_employes&id=". $item["id"]); ?>&idName=id">Delete</a></li>
                    <?php
                }
                // Index de recherche?

                foreach ($taches as $item) {
                    ?>
                    <li class="taches">
                        <a id="tache<?php echo $item["id"]; ?>" class="taches"
                           href="#"
                           onclick="mySearchFunctionChooseTheme('tache<?php echo $item["id"]; ?>', <?php echo $item["id"]; ?>)"><?php
                            echo $item["nom_activite"] . " " . $item["nom_employe"] . " " . $item["prenom_employe"] . " " . $item["nom_patient"] . " " . $item["prenom_patient"]; ?></a>
<?php                echo $item["nom_patient"] . " " . $item["prenom_patient"]."</a>&nbsp;</li>";
?>
                   &nbsp;<a href="<?php echo make_link("?page=tables&action=edit&table=table_taches&id=". $item["id"]); ?>&idName=id">Edit</a>
                &nbsp;<a href="<?php echo make_link("?page=tables&action=delete&table=table_taches&id=".$item["id"]); ?>&idName=id">Delete</a></li>


                    <?php

                }

                    global $halfHour, $days, $j, $m, $a, $days;
                    $j = explode(":", $item["jour__semaine_demie__heure_temps"])[0];
                    $h = explode(":", $item["jour__semaine_demie__heure_temps"])[1];
                    $d = explode(":", $item["jour__semaine_demie__heure_temps"])[2];

                foreach ($activites as $item) {
                    ?>
                    <li class="activites">
                        <a id="activite<?php echo $item["id"]; ?>" class="activites"
                           href="#" onclick="mySearchFunctionChooseTheme('activite<?php echo $item["id"]; ?>')"><?php
                            echo $item["nom_activite"] . " avec " . $item["nom"] . " " . $item["prenom"]; ?></a>
                        &nbsp;<a href="<?php echo make_link("?page=tables&action=edit&table=table_activites&id=".($item["id"])); ?>&idName=id">Edit</a>
                &nbsp;<a href="<?php echo make_link("?page=tables&action=delete&table=table_activites&id=".($item["id"])); ?>&idName=id">Delete</a></li>
                    <?php
                }
                foreach ($days as $j => $day) {
                    ?>
                    <li class="jour__semaine_demie__heure_temps_0">
                        <a id="jour__semaine_demie__heure_temps_0<?php echo $j; ?>"
                           class="jour__semaine_demie__heure_temps_0"
                           href="#"
                           onclick="mySearchFunctionChooseTheme('jour__semaine_demie__heure_temps_0<?php echo $j ?>', '<?php echo $j ?>')">
                            <?php
                            echo $day; ?></a></li>
                    <?php
                }
                foreach ($halfHour as $j => $h) { ?>
                    <li class="jour__semaine_demie__heure_temps_1">
                        <a id="jour__semaine_demie__heure_temps_1<?php echo $h; ?>"
                           class="jour__semaine_demie__heure_temps_1"
                           href="#"
                           onclick="mySearchFunctionChooseTheme('jour__semaine_demie__heure_temps_1<?php echo $h; ?>', '<?php echo $h ?>')"><?php
                            echo $h; ?></a>&nbsp;</li>
                    <?php
                }
                for ($d = 0; $d < 10; $d++) { ?>
                    <li class="jour__semaine_demie__heure_temps_2">
                    <a id="jour__semaine_demie__heure_temps_2<?php echo $d; ?>"
                       class="jour__semaine_demie__heure_temps_2"
                       href="#"
                       onclick="mySearchFunctionChooseTheme('jour__semaine_demie__heure_temps_2<?php echo $d; ?>', '<?php echo $d ?>')"><?php
                        echo $d / 2; ?></a></li>
                    <?php
                }
                ?>
            </ul>
        </td>
    </tr>
</table>


<table id="notes">
<?php
$sql = "select text_note, date_now, nom_utilisateur from table_notes order by date_now";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll();

foreach($results as $rowItem) {
    ?>
    <tr><td><?php echo htmlentities($rowItem["date_now"]);?></td><td><?php echo htmlentities($rowItem["text_note"]); ?></td>
    <td><?php echo htmlentities($rowItem["nom_utilisateur"]);?></td></tr>
    <?php
}
?>
</table>
