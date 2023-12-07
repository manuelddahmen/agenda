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
global $db;
require_once "db.php";
require_once "db2.php";

$user_id = -1;
$id = (int)(isset($_GET["id"]) ? $_GET["id"] : -1);
$PATIENT_ID = 630;

class Data
{
    public int $id;
    public int $site;
    public string $user_id;
    public string $patient_name;
    public int $patient_id;
    public string $groupe_name;
    public string $personnel_name;
    public int $groupe_id;
    public int $personnel_id;
    public mixed $day;
    public mixed $hour;
    public mixed $quart;
}

class Results
{
    public array $data = array();


    public function __construct()
    {
        $this->fillAll();
    }

    public function fillAll()
    {
        global $db;


        $sql = "select chambre as patient_id, tp.id as personnel_id, groupe_id, nom as patient_name, quart, hour, day, 
       tg.name as groupe_name, tp.name personnel_name, tac.id as id from table_hospitalises as patients
    inner join table_activite_christine as tac on patients.user_id = tac.user_id 
 and patients.chambre=tac.patient_id inner join table_personnel as tp on patients.user_id = tp.user_id and tac.personnel_id=tp.id
 inner join table_groupes as tg on patients.user_id = tg.user_id and tg.id=tac.groupe_id where personnel_id>0 and groupe_id>0;";


        $stmt = $db->prepare($sql);

        //echo $sql;

        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $result) {
            $this->add($result);
        }

    }

    private function add($result)
    {
        global $PATIENT_ID;
        $data = new Data();

        $this->data[] = $data;

        $data->id = $result["id"];
        $data->patient_id = $result["patient"] ?? $PATIENT_ID;
        $data->patient_name = $result["patient_name"];
        $data->personnel_id = $result["personnel_id"];
        $data->groupe_id = $result["groupe_id"];
        $data->personnel_name = $result["personnel_name"];
        $data->groupe_name = $result["groupe_name"];
        $data->day = $result["day"];
        $data->hour = $result["hour"];
        $data->quart = $result["quart"];
        $data->user_id = $result["user_id"] ?? -1;
    }
}

function loadData(): void
{
    global $personnel;
    global $groupes;
    global $db;
    $sqlPersonnel = "select * from table_personnel;";

    $sqlGroupes = "select * from table_groupes;";

    $stmtP = $db->prepare($sqlPersonnel);

    $resultsP = $stmtP->execute();

    $resultsP = $stmtP->fetchAll();

    $stmtG = $db->prepare($sqlGroupes);

    $resultsG = $stmtG->execute();

    $resultsG = $stmtG->fetchAll();


    global $currentEvent;
    ?>


    <select id="personnel" name="personnel">
        <?php
        $i = 0;
        while ($i < count($resultsP)) {
            $r = $resultsP[$i];
            $i++;
            ?>
            <option <?php echo ($r["id"] == $personnel || (isset($currentEvent) && $r["id"] == $currentEvent["personnel"])) ? 'selected=\"selected\"' : ''; ?>
                id="personnel<?php echo $r["id"]; ?>" value="<?php echo $r["id"]; ?>"><?php echo $r["name"]; ?></option>
            <?php
        }
        ?>
    </select>
    <select id="groupes" name="groupes">
        <?php
        $i = 0;
        while ($i < count($resultsG)) {
            $r = $resultsG[$i];
            $i++;
            ?>
            <option <?php echo ($r["id"] == $groupes || (isset($currentEvent) && $r["id"] == $currentEvent["groupe_id"])) ? 'selected=\"selected\"' : ' '; ?>
                id="groupes<?php echo $r["id"]; ?>" value="<?php echo $r["id"]; ?>"><?php echo $r["name"]; ?></option>
            <?php
        }
        ?>
    </select>
    <?php

}


$w_o_d = isset($_GET["data"]) ? $_GET["data"] : "week";
$day = (int)(isset($_GET["day"]) ? $_GET["day"] : 0);
$hour = isset($_GET["hour"]) ? $_GET["hour"] : "8";
$quart = isset($_GET["quart"]) ? $_GET["quart"] : "0";
$groupes = isset($_GET["groupes"]) ? $_GET["groupes"] : "";
$personnel = isset($_GET["personnel"]) ? $_GET["personnel"] : "";


$currentEvent = array();
if (isset($_GET["id"]) && $_GET["id"] > 0) {
    $currentEvent["id"] = $_GET["id"];

    $sql = "select * from table_activite_christine where id=" . ((int)($currentEvent["id"]));

    global $db;

    $stmt = $db->prepare($sql);

    $result = $stmt->fetchAll();

    if ($result != NULL) {
        $currentEvent["user_id"] = $result["user_id"];
        $currentEvent["groupe_id"] = $result["groupe_id"];
        $currentEvent["day"] = $result["day"];
        $currentEvent["hour"] = $result["hour"];
        $currentEvent["quart"] = $result["quart"];
        $currentEvent["personnel_id"] = $result["personnel_id"];
        $currentEvent["patient_id"] = $result["patient_id"];
    }
}


$jours = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi");
$hours = array(8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
$quarts = array(0, 15, 30, 45);
function displayWeek()
{
    global $hours;
    global $quarts;
    ?>
    <table class="week">
        <tr>
            <?php
            for ($i = 0; $i < 5; $i++) { ?>
                <td>
                    <a href="?data=day&day=<?php echo $i; ?>">
                        <?php echo layoutDay($i); ?></a></td>
                <?php
            }
            ?></tr>


        <?php
        foreach ($hours as $hour) {
            foreach ($quarts as $quart) {
                ?><tr><?php
                for ($i = 0; $i < 5; $i++) {

                    layoutHour($i, $hour, $quart);

                }
                echo "</tr>";
            }
            ?></tr><?php
        }
        ?>


    </table>
    <?php
}

function layoutDay(int $day_of_week)
{
    global $jours;
    ?><a href="?data=day&day=<?php echo $day_of_week; ?>"><?php echo $jours[(int)$day_of_week]; ?></a>
    <?php
}

function layoutHour(int $day_of_week, int $hour, int $quart)
{

    global $data;
    global $jours;
    $results = new Results();
    $data = $results->data;
    echo "<td class='popup' onclick='TableForm(this)'>";
    echo makeLink("hour", "view", -1, $day_of_week, $hour, $quart) . "$hour:$quart" . "</a>";
    if ($data != null && count($data) > 0) {
        foreach ($data as $d) {
            $id_data = $d->id;
            if ($d->quart == $quart && $d->day == $day_of_week && $d->hour == $hour) {
                ?><a
                href='?data=hour&action2=edit&day=<?php echo $day_of_week; ?>&hour=<?php echo $hour; ?>&quart=<?php echo $quart; ?>&id=<?php echo $id_data; ?>'>
                    Edit</a>
            <a href='?data=hour&&action2=delete&day=<?php echo $day_of_week; ?>&hour=<?php echo $hour; ?>&quart=<?php echo $quart; ?>&id=<?php echo $id_data; ?>'>
                    Delete</a><?php
                echo "<table><tr><td>Groupe</td><td>" . $d->groupe_name . "</td></tr>";
                echo "<tr><td>Personnel</td><td>" . $d->personnel_name . "</td></tr>";
                echo "<tr><td>Patient</td><td>" . $d->patient_name . "</td></tr></table>";
            }
        }
    }
    echo "</td>";
}

function makeLink($data = "", $action2 = "", $id = -1, $day, $hour, $quart)
{
    if ($data == "")
        global $data;
    return "<a href='?data=$data&action2=$action2&day=$day&hour=$hour&quart=$quart&id=$id'>";
}

function displayHours(int $day_of_week)
{
    global $hours;
    global $quarts;
    global $hour;
    global $quart;
    global $day;
    foreach ($hours as $hour) {

        foreach ($quarts as $quart) {
            layoutHour($day_of_week ?? $day, $hour, $quart);
        }
    }
}

function displayHour()
{
    global $day;
    global $hour;
    global $quart;
    global $jours;
    ?><a href="?data=day&day=<?php echo $day; ?>&hour=<?php echo $hour; ?>&quart=<?php echo $quart; ?>">
    <?php echo $jours[(int)$day]; ?>
    </a>
    <?php layoutHour((int)$day, (int)$hour, (int)$quart); ?><?php
}

function displayDay() {
global $hours;
global $quarts;
global $jours;
global $day;
if ($day < 0 || $day >= 5)
    $day = 0;
?><a href="?data=week&day=<?php echo $day; ?>">Week</a>
<table class="week">
    <tr>
        <td><?php layoutDay((int)$day); ?></td>
    <?php
    foreach ($hours as $hour) {
        foreach ($quarts as $quart) {
            ?><tr><?php
            for ($i = $day; $i < (int)$day + 1; $i++) {
                layoutHour((int)$i, (int)$hour, (int)$quart);
            }
            echo "</tr>";
        }
        ?></tr><?php
    }
    }
    global $w_o_d;
    ?>


    <html>
    <head>
        <title><?php echo "Planning"; ?></title>
        <script type="application/javascript" src="../js/agenda_christine.js"></script>
        <link rel="stylesheet" href="../css/<?php
        $themeName = $themeName ?? "light";
        echo $themeName; ?>/agenda.css" type="text/css">
        <link rel="stylesheet" href="../css/<?php
        $themeName = $themeName ?? "light";
        echo $themeName; ?>/search_menu.css" type="text/css">
        <link rel="stylesheet" href="../css/christine/agenda.css" type="text/css" media="screen">
        <link rel="stylesheet" href="../css/christine/print.css" type="text/css" media="print">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


    </head>
    <body>
    <h1>Planning</h1>
    <div id="time_view">
        <?php if ($w_o_d == "week") displayWeek(); ?>
        <?php if ($w_o_d == "day") displayDay(); ?>
        <?php if ($w_o_d == "hour") displayHour(); ?>
    </div>

    <?php
    function displayEvent($id) {
    global $hours;
    global $quarts;
    global $hour;
    global $quart;
    global $day;
    global $w_o_d;
    global $db;
    global $PATIENT_ID;
    ?>
    <form id="add_groupe_personnel">
        <input type="hidden" name="day" value="<?php echo $currentEvent["id"] ?? $day; ?>"/>
        <input type="hidden" name="hour" value="<?php echo $currentEvent["hour"] ?? $hour; ?>"/>
        <input type="hidden" name="quart" value="<?php echo $currentEvent["quart"] ?? $quart; ?>"/>
        <input type="hidden" name="data" value="<?php echo $w_o_d; ?>"/>

        <?php
        global $action2;
        global $currentEvent;
        if ($action2 == "edit") {
            ?><input type="hidden" name="id" value="<?php echo $currentEvent["id"] ?: $id; ?>"/><?php
        } else {

        }
        loadData($currentEvent); ?>
        <button type="submit" name="add" onclick="//javascript:addActivite(this)">Ajouter/Modifier</button>

        <?php

        $action2 = $_GET["action2"] ?? "view";
        if (isset($_GET["add"])) {
            global $day;
            global $hour;
            global $quart;
            global $groupes;
            global $personnel;
            global $user_id;
            global $id;

            if ($id <= 0)
                $id = rand(1000000, 10000000000);


            $sql = "insert into table_activite_christine (id, user_id, site, patient_id, personnel_id, groupe_id, day, hour, quart) values (" . $id . "," . (($user_id != null) ? $user_id : -1) . ", '1', $PATIENT_ID, $personnel, $groupes, $day, $hour, $quart)";

            $stmt = $db->prepare($sql);
            if ($stmt->execute())
                echo "Inserted";
            else
                echo "ERROR Inserting $sql";
        }
        if ($action2 == "delete") {
            global $hour;
            global $quart;
            global $groupes;
            global $personnel;
            global $user_id;
            global $id;

            $sql = "delete from table_activite_christine where id=" . ((int)$id) . ";";

            $stmt = $db->prepare($sql);
            if ($stmt->execute())
                echo "NO ERROR INserting";
            else
                echo "NO ERROR INserting $sql";
        }

        }


        displayEvent($id);
        ?>

    </form>
    <!--    <td draggable="true" ondragstart="drag(event)">Contenu de la cellule</td>
        <td ondragover="allowDrop(event)" ondrop="drop(event)">Contenu de la cellule</td>
    -->
    </body>
    </html>

