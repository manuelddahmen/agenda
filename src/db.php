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


require_once "navigation.php";
require_once "MyDB.php";
require_once "functions.php";
require_once '../vendor/autoload.php';
require_once "AgendaUser.php";
global $days, $halfHour;
global $db;

use JetBrains\PhpStorm\NoReturn;
use PHPSQLParser\PHPSQLParser;




if(!isset($id_hospitalise)) {
    $i=0;

    $id_hospitalise = array();
    if(isset($_GET["id_hospitalise"]) && is_scalar($_GET["id_hospitalise"])) {
        foreach ($_GET as $key => $value) {
            $id_hospitalise[$i] = $_GET["id_hospitalise"];
            $i++;
        }
    } else if(isset($_GET["id_hospitalise"]) && is_array($_GET["id_hospitalise"])) {
        foreach ($_GET as $key => $value) {
            if (array_search($value, $id_hospitalise)===false) {
                $id_hospitalise[$i] = $_GET["id_hospitalise"];
                $i++;
            }
        }
    }
    foreach ($_GET as $key => $value) {
        if(str_starts_with("id_hospitalise_", $key)) {
            if (array_search($value, $id_hospitalise)===false) {
                $id_hospitalise[$i] = $value;
                $i++;
            }
        }
    }
    foreach ($_GET as $key => $value) {
        if (str_starts_with("id_hospitalises_", $key)) {
            if (array_search($value, $id_hospitalise) === false) {
                $id_hospitalise[$i] = $value;
                $i++;
            }
        }
    }
}

$db = new MyDB();

function printFormChooseFk($tablename, $fkName, $idfkCurrent): void
{
    $tableDescription = getTableDetails($tablename);
    $tablenameReference = $tableDescription["fk"][$fkName]["tablename"];
    $fieldReference = $tableDescription["fk"][$fkName]["fieldName"];
    $sql = "select " . $fieldReference . ", * from " . $tablenameReference . ";";
    $db = new MyDB();
    $stmt = $db->prepare($sql);
    $results = $stmt->execute();


    foreach ($results as $key => $value) {
        ?>
        <select name="<?php echo $fkName; ?>">
            <option value="<?php echo $value[$fieldReference]; ?>">
                <?php print_r($value); ?>
            </option selected='"<?php echo ($value[$fieldReference] ==
                $idfkCurrent) ? "selected" : ""; ?>"'>

        </select>
        <?php
    }
}


function findInfoTable($tablename)
{

    return getTableDetails($tablename)[0];
}

function char1($type, $fieldName): string
{
    if (isset($fieldName) && isset($type)
        && ((strstr($type, "varchar") != 0||strstr($type, "string") != 0)
            || strstr($type, "datetime") != 0
            || strstr($type, "date") != 0)) {
        $char1 = "'";
    } else {
        $char1 = "";
    }
    return $char1;
}
function crud() : void
{
    global $action;
    global $db;
    if (isset($_GET["action"])) {
        $action = urldecode($_GET["action"]) ?? "";
    } else {
        $action = "";
    }

    if ($action == "select") {
        $idpk = urldecode($_GET["id"]);
        $idpkName = urldecode($_GET["idName"]);
        $idfk = urldecode($_GET["idfk"]);
        $idfkName = urldecode($_GET["idfkName"]);
        $tablename = urldecode($_GET["tablename"]);
    } else if ($action == "updateFk") {
        $id = urldecode($_GET["id"]);
        $idName = urldecode($_GET["idName"]);
    }
///???
    if ($action != "") {
        $str = "";

        $sql = "SELECT * FROM sqlite_master WHERE type ='table' AND name LIKE 'table_%';";
        /*    printTable("sqlite_master", array("type", "name", "tbl_name", "sql"),
                array("varchar", "varchar", "tbl_name",
                    "integer", "varchar"), array("rootpage"), array("integer"), "schema");;
        */
        if ($action == "save") {

            foreach ($_GET as $keyName => $fieldValue) {
                if (str_starts_with($keyName, "data-")) {
                    $fieldName = substr($keyName, strlen("data-"));
                    $fields[$fieldName] = $fieldValue;
                } else {
                    $fieldName = $keyName;
                    //$fields[$fieldName] = $fieldValue;

                }
            }
            $idName = $_GET['idName'] ?? $fields["idName"];
            $id = $fields[$idName] ?? $_GET[$idName];
            $fields["data-" . $idName] = $id;
            $table = $_GET['table'];

            global $userData;



            //$sql = "select * from " . $table . ($table=="table_users"?"":" where user_id=".$userData["id"] ).";";
            //$stmt = $db->prepare($sql);
            //$stmt->execute();
            //$result = $stmt->fetchAll();
            $types = findInfoTable($table);
            if ($_GET["data-" . $idName] !== 0) {
                $sql = "update " . $table . " set ";
                foreach ($fields as $name => $value) {
                    if (isset($types[$name])) {
                        // TODO File types: integer, string,datetime
                        $char1 = "";
                        if ($value != NULL && $value != "" && $value != -1) {
                            $sql .= $name . "=" . char1($types[$name], $name) . escapeSqlChars($value)
                                . char1($types[$name], $name) . ",";
                            $values[$name] = $value;
                        }
                    }
                }
                $sql = trim($sql, ",");
                $sql .= " where " . $idName . "=" .
                    char1($types[$idName], $idName) . escapeSqlChars($id) . char1($types[$idName], $idName)
                    . ($table == "table_users" ? "" : " and user_id=" . $userData["id"]) . ";";
                $values[$idName] = $id;

                //echo "UPDATE = " . $sql.";";
                //echo "UPDATE = " . $sql;

                //$stmt = $db->query($sql);
                //$stmt->execute($values);



                $db->exec($sql);
            }

            if (isset($_GET["request"])) {
                echo "OK";
                exit(0);
            }
        } else if ($action == "saveNew") {
            $idName = $_GET['idName'];
            $id = $_GET[$idName] ?? 0;
            $table = $_GET['table'];


            if ($table == "table_users") {
                $sql = "select * from table_users where email=:email;";
                $fieldValue = $_GET["data-email"];
                $stmt = $db->prepare($sql);

                /* Bind variables to parameters */
                $stmt->bindParam(":email", $fieldValue);
                $stmt->execute(array($fieldValue));


                $result = $stmt->fetchAll();
                if(count($result)>0) {
                    echo "<ul><li class='sousmenu'>Erreur utilisateur existe.</li></ul>";
                    echo "<ul><li class='sousmenu'>Impossible de créer un utilisateur pour le moment</li></ul>";
                    exit();
                }
            }

            $fields = array();
            foreach ($_GET as $keyName => $fieldValue) {
                if (str_starts_with($keyName, "data-")) {
                    if ($fieldValue != NULL && $fieldValue != "" && $fieldValue != -1) {
                        $fieldName = substr($keyName, strlen("data-"));
                        $fields[$fieldName] = $fieldValue;

                    }
                }
            }

            global $userData;
            $sql = "select * from " . $table . ($userData != NULL ? " where user_id=" . ($userData["id"]) : "") . ";";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $types = findInfoTable($table);

            $sql1 = "";
            $sql2 = "";

            if (isset($_GET["data-" . $idName]) || isset($_GET["id"])) {
                $sql1 = "insert into " . $table . " (";
                $sql2 = ") values (";
                foreach ($fields as $name => $value) {
                    if ($name != "user_id") {
                        // TODO File types: integer, string,datetime
                        $char1 = "";
                        if ($value != NULL && $value != "" && $value != -1) {
                            $sql1 .= $name . ",";
                            $sql2 .= char1($types[$name], $name) . escapeSqlChars($value) . char1($types[$name], $name) . ",";
                            $values[] = $value;
                        }
                    }

                }


                $sql1 = trim($sql1, ",");
                $sql2 = trim($sql2, ",");

                if ($table != "table_users") {
                    $sql1 .= ", user_id";
                    $sql2 .= ", " . ($userData["id"]);
                }
            }
            $sql = $sql1 . $sql2 . ");";

            addError("Notice", $sql);
            //$stmt = $db->prepare($sql);
            //$stmt->execute($values);
            //echo($sql);
            $db->exec($sql);
            if (isset($_GET["request"])) {
                echo "OK";
                exit(0);
            }
        } else if ($action == "delete") {
            global $userData;
            $idName = urldecode($_GET['idName']);
            $id = (int)(urldecode($_GET['id']) ?? $_GET[$idName]);
            $sql = "delete from " . urldecode($_GET["table"]) . " where " . $idName . "=" . $id .
                " and user_id=" . ($userData["id"]) . ";";
            //$stmt = $db->prepare($sql);
            //$stmt->execute();
            $db->exec($sql);
            if (isset($_GET["request"])) {
                echo "OK";
                exit(0);
            }
        } else if ($action == "add") {
            $str .= printFormEdit($_GET["table"], $_GET["idName"], 0, false, array(), true);
        } else if ($action == "edit") {
            $str .= printFormEdit($_GET["table"], $_GET["idName"], $_GET["id"], true, array(), true);
        } else {
            $str .= passForm($_GET, urldecode($action));

            if (isset($_GET["request"])) {
                return ;
            }
        }

        echo $editAddDeleteStr = $str;
    }
    return;
}

crud();

function utilTableLinks($ids, $idsName, $idsType)
{

}

function utilTableLinksIds($ids, $idsName, $idsType): string
{
    $ids2 = "" . $idsName[0] . "=" . $ids[0] . "&";
    for ($id = 1; $id < count($idsName); $id++) {
        $ids2 .= $idsName[$id] . "=" . $ids[$id] . "&";
    }
    return trim($ids2, '&');
}

function utilTableRowWithId(
    $columns, $columnsNames, $columnsType,
    $ids, $idsName, $idsType, $formId, $insert = false)
{

}

/***
 * @param $tablename string
 * @param $columnsNames array
 * @param $columnsType array
 * @param $idsName array
 * @param $idsType array of strings 'varchar' 'integer' 'date' datetime'
 * @param $idForm string
 * @param $foreignKeys array|null("$idName"=>array(("tablename=>"$tablename", "references"=> "field2$", "display" =>"$fieldName), "key2"=>)
 * @param string $finishPage
 * @param bool $displayId
 * @return string
 */
function printTable(string $tablename, array $columnsNames,
                    array  $columnsType, array $idsName, array $idsType,
                    string $idForm, array $foreignKeys = null, string $finishPage = "tables", bool $displayId=false): string
{

    if (count($columnsNames) == 2 &&
        isset($columnsNames[0]) && isset($columnsNames[1]) &&
        is_array($columnsNames[0]) && is_array($columnsNames[1])) {
        $columnsNamesReplaced = $columnsNames[1];
        $columnsNames = $columnsNames[0];
    }
    $str = "";
    global $db;
    global $userData;
    if($userData!=null)
        $user_id = $userData["id"];
    if (!$db) {
        error_log("No database connection in printTable");
    } else {
        //echo "Opened database successfully\n";
    }
    $id2 = '';
    for ($i = 0; $i < count($idsName) - 1; $i++)
        $id2 = $id2 . ",";
    $id2 = $id2 . "," . $idsName[count($idsName) - 1];
    $id2 = trim($id2, ',');

    $fkStr1 = "";
    $fkStr2 = "";
    $i = 1;
    $fks = array();
    $results = array();
    $fks = array();
    if ($foreignKeys != null) {
        foreach ($foreignKeys as $fk => $paramsFK) {
            $field1 = $fk;
            $id2ref = $paramsFK["references"];
            $table2 = $paramsFK["tablename"];
            $display = $paramsFK["display"];
            $displays = implode(", ", $display);
            $fks[$field1] = $paramsFK;
            $fks[$field1]["idName"] = $id2ref;
            $sql1 = "select $id2ref, $displays from $table2 where user_id=:userId;";
            $stmt = $db->prepare($sql1);
            $stmt->bindParam("userId", $user_id);
            $stmt->execute();
            $results[$field1] = $stmt->fetchAll();
            $i++;
        }
    }

    global $userData;
    $sql = "select t1." . $id2 . ", * from " . $tablename . " t1"." where user_id=".($userData["id"]).";";
    //echo $sql;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    //$stmt->setFetchMode(PDO::FETCH_CLASS);
    $result = $stmt->fetchAll();


    $tableHtmlName = "printTable_" . $tablename;


    $str .= "<table border='4' class='printTable' id='$tableHtmlName'>";

    $str .= "<tr>";
    if($displayId) {
        for ($c = 0; $c < count($idsName); $c++) {

            $str .= "<td class='title'>" . $idsName[$c] . "</td>";
        }
    }
    for ($c = 0; $c < count($columnsNames); $c++) {
        $str .= "<td class='title'>" .
            (isset($columnsNamesReplaced[$c]) ? $columnsNamesReplaced[$c] : $columnsNames[$c]) . "</td>";
    }
    $keySupp = array();
    if ($result != null && count($result) > 0) {


        foreach ($result[0] as $key => $value) {
            if (!in_array($key, $idsName, true) &&
                !in_array($key, $columnsNames, true)) {
                if($key!=="user_id") {
                    $str .= "<td class='title'>" . $key . "</td>";
                $keySupp[count($keySupp)] = $key;
                }

            }
        }
    }


    for ($i = 0; $i < count($result); $i++) {
        $str .= "<tr>";
        if($displayId) {
            for ($c = 0; $c < count($idsName); $c++) {
                $str .= "<td><p >" . $result[$i][$idsName[$c]] . "</p></td>";
            }
        }
        for ($c = 0; $c < count($columnsNames); $c++) {
            if("user_id" == $columnsNames) {
                continue;
            }
            $str .= "<td".(!isset($foreignKeys[$columnsNames[$c]])?" contenteditable='true'":"").">";

            $fieldNameHtml = $tableHtmlName . "_" . $i . "_" . ($columnsNames[$c]);

            if (isset($foreignKeys[$columnsNames[$c]])) {

                $str .= "<select name='" . ($columnsNames[$c]) . "' id='$fieldNameHtml' onchange='commitChanges(\"$fieldNameHtml\")'><option value='-1' class='error'>Non attribué</option>";
                foreach ($results[$columnsNames[$c]] as $rowItem) {
                    $str .= "<option " . (($result[$i][$columnsNames[$c]] == $rowItem[$fks[$columnsNames[$c]]["idName"]]) ? "selected='selected'" : "") . " value='";
                    $str .= $rowItem[$fks[$columnsNames[$c]]["idName"]];
                    $str .= "'>";
                    foreach ($foreignKeys[$columnsNames[$c]]["display"] as $value) {
                        $str .= " " . $rowItem[$value];
                    }

                    $str .= "</option>";
                }
                $str .= "</select>";
            } else {
                $str .= "<p id='$fieldNameHtml' onchange='commitChanges(\"$fieldNameHtml\")' onclick='commitChanges(\'$fieldNameHtml\')'>" . $result[$i][$columnsNames[$c]] . "</p>";
            }
            $str .= "</td>";
        }
        for ($c = 0; $c < count($keySupp); $c++) {
            $str .= "<td>" . $result[$i][$keySupp[$c]] . "</td>";
        }

        $ids = "&" . $idsName[0] . "=" . $result[$i][$idsName[0]] . "&" . $result[$i][$idsName[0]];
        $ids1 = array();
        $ids1[0] = $result[$i][$idsName[0]];
        for ($id = 1; $id < count($idsName); $id++) {
            $ids1[$id] = $result[$i][$idsName[$id]];
            $ids .= "?&$idsName[$id]=" . $result[$i][$idsName[$id]];
        }
        $str .= "<td>";
        $str .= print_edit_link($tablename, $idsName, $idsType,
            $ids1);
        $str .= "</td>" . "<td>";
        $str .= print_delete_link($tablename, $idsName, $idsType,
            $ids1);
        $str .= "</td>" . "</tr>";
    }
    $str .= "</table>";
    $str .= print_add_link($tablename, $idsName, $idsType, 0);

    return $str;
}

function printTableSql($sql, $idName, $tablename): string
{
    $str = "";
    $db = new MyDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $str .= "<table id='edit_table' onload='hasChanged = true;'>";

    $str .= "<tr>";
    if ($result != null && count($result) > 0) {
        foreach ($result[0] as $key => $value) {
            $str .= "<td>" . $key . "</td>";
        }


        for ($i = 0; $i < count($result); $i++) {
            $str .= "<tr>";
            foreach ($result[$i] as $key => $value) {
                $str .= "<td>" . escapeSqlChars($value) . "</td>";
            }

            $id = "&" . $idName . "=" . $result[$i][$idName] . "&" . $result[$i][$idName];
            $str .= "<td>";
            $str .= print_edit_link($tablename, array($idName), array("int"), array($id));
            $str .= "</td>" . "<td>";
            $str .= print_delete_link($tablename, array($idName), array("int"), array($id));
            $str .= "</td>" . "</tr>";
        }
        $str .= "</table>";
        $str .= print_add_link($tablename, array($idName), array(), array(0));
    }

    return $str;
}
function print_delete_link1($tablename, $idsName, $idsType = array(), $ids): string
{
    $str = "";
    $str .= "<a class='editLink btn' href='?page=tables&action=delete&table=" . $tablename . "&" . "id=" . ($ids[0]) . "&idName=" . ($idsName[0]) . "' ><img src='../images/delete.png' alt='Delete item from " . $tablename . "\"');'/></a>";
    return $str;
}

function print_edit_link1($tablename, $idsName, $idsType = array(), $ids = 0): string
{
    $str = "";
    $str .= "<a class='editLink btn' href='?page=tables&action=edit&table=" . $tablename . "&" . "id=" . ($ids[0]) . "&idName=" . ($idsName[0]) . "' ><img src='../images/modify.png' alt='Edit item from " . $tablename . "\"');'/></a>";

    return $str;
}


function print_add_link1($tablename, $idsName, $idsType = array()): string
{
    $str = "";
    $str .= "<a href='?page=tables&action=add&table=" . $tablename . "&" . "id=" . (0) . "&idName=" . ($idsName[0]) . "' alt='Add item to table $tablename'><img src='../images/add.png' height='40px' width='40px' /></a>";
    return $str;
}
function print_delete_link($tablename, $idsName, $idsType = array(), $ids): string
{
    $url = "\"javascript:include2('ajax/request_form.php?page=tables&action=delete&table=" . $tablename . "&" . "id=" . ($ids[0]) . "&idName=" . ($idsName[0]) ."');\"";
    $str = "<a class='deleteLink btn' href='#' onclick=".$url."><img src='../images/delete.png' alt='Delete item from table' " . $tablename . " height='40px' width='40px' ></a>";
    return $str;
}

function print_edit_link($tablename, $idsName, $idsType = array(), $ids = 0): string
{
    $url = "\"javascript:include2('ajax/request_form.php?page=tables&action=edit&table=" . $tablename . "&" . "id=" . ($ids[0]) . "&idName=" . ($idsName[0]) ."');\"";
    return "<a class='editLink btn' href='#' onclick=".$url ."><img src='../images/modify.png' alt='Edit item from " . $tablename . "\"'); height='40px' width='40px' '/></a>"
        ;
}


function print_add_link($tablename, $idsName, $idsType = array()): string
{
    $url = "\"javascript:include2('ajax/request_form.php?page=tables&action=add&table=" . $tablename . "&" . "id=" . (0) . "&idName=" . ($idsName[0]) ."');\"";
    return "<a  href='#' onclick=$url ><img alt='Add item to table $tablename.' src='../images/add.png' height='40px' width='40px' /></a>";
}


/***
 * @param $tableName
 * @param $idName
 * @param $id
 * @param bool $edit
 * @param array $data
 * @param $autoId
 * @return string
 */
function printFormEdit($tableName, $idName, $id, bool $edit = true, array $data = array(), bool $autoId=true): string
{
    global $userData;

    $pageDetail = $_GET["page"];
    $str = "<script language='JavaScript' type='text/javascript'>hasChanged = true;</script>
            <form method='GET' id='table_edit' onload='//requireConfirmOnReload();' action='index.php'> ";
    $tid = "edit_table_$tableName";
    $str .= "<h2 class='edit_table_item'>Edition/Ajout item de : $tableName</h2><table id='$tid' class='edit_table_item'>";
    global $db;


    $result0 = getTableDetails($tableName);

    if (!$edit) {
        $result = getTableDetails($tableName);
        $idRef = $id;


    } else { // Check fields idName
        if (isset($result0[0]["idName"])) {

        }
        $sql = "select * from " . $tableName . " where " . $idName . "=" . $id .($tableName=="table_users"?"":" and user_id=".$userData["id"] ).";";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $idName = isset($_GET["idName"]) ? urldecode($_GET["idName"]) : $idName;
        $idRef = isset($result[0][$idName]) ?? $id;


    }

    global $userData;
    $sqlNewRow = "select max(" . $idName . ")+1 as max from " . $tableName.";"; //.
        //($tableName==="table_users"?"":" where user_id=".$userData["id"] ).";";
    $stmtNewRow = $db->prepare($sqlNewRow);
    $stmtNewRow->execute();
    $resultNewRow = $stmtNewRow->fetchAll();
    $idMax1 = ((int)($resultNewRow[0]["max"]));

    global $tables;
    if (count($tables[$tableName]) > 0) {
        foreach ($tables[$tableName] as $fk => $paramsFK) {
            $field1 = $fk;
            $id2ref = $paramsFK["references"];
            $table2 = $paramsFK["tablename"];
            $display = $paramsFK["display"];
            $fks[$field1] = $paramsFK;
            $fks[$field1]["idName"] = $id2ref;
            $fks[$field1]["display"] = $display;
            $fks[$field1]["table"] = $table2;
            $sql1 = "select $id2ref," . implode(',', $display) . " from $table2".($table2=="table_users"?"":" where user_id=".$userData["id"] ).";";
            $stmt = $db->prepare($sql1);
            $stmt->execute();
            $results[$field1] = $stmt->fetchAll();
        }
    }
    if ($result != null) {
        foreach ($result[0] as $key => $value) {
            if (isset($result0[0][$key])) {
                $fieldValue = $edit ? $value : "";
                $idRow = false;
                if (($key == $idName && $autoId)||$key=="user_id") {
                    $idRow = true;
                }
                if ($key == $idName && !$edit) {
                    $fieldValue = $idMax1;
                }

                $str .= "<tr class='".($idRow?"idrow":"")."'><td class='title ligne2 btn-choose'>" . $key . "</td><td>";
                if(!$idRow) {
                    if (isset($tables[$tableName][$key])) {
                        $str .= "<select class='btn-choose ligne2' name='data-" . $key . "'><option value='-1' class='error'>Aucun lien</option>";
                        foreach ($results[$key] as $rowItem) {
                            $str .= "<option";
                            $str .= " " . ($rowItem[$fks[$key]["idName"]] == $value ? "selected='selected'" : "") . "  value='";
                            $str .= $rowItem[$fks[$key]["idName"]] . "'>";
                            foreach ($fks[$key]["display"] as $referencedFieldName) {
                                $str .= $rowItem[$referencedFieldName];
                            }
                            $currentPkTable = $fks[$key]["table"];
                            $str .= "</option>";
                        }
                        $str .= "</select>";
                    } else if (str_contains($key, "date")) {
                        $currentPkTable = false;
                        $str .= "<input type='date' 
                name='data-" . $key . "' "
                            . " value='"
                            . $fieldValue . "'>";
                    } else {
                        $str .= "<input type='text' 
                name='data-" . $key . "' "
                            . " value='"
                            . $fieldValue . "'>";
                    }
                }
                $str .= "</td><td>";
                if (isset($currentPkTable) && $currentPkTable != null) {
                    //$str .= "<a class='btn-choose ligne2' href='agenda.php?page=tables&action=add&table=" . $currentPkTable . "&id=-1&idName=" . $fks[$key]["idName"] . "' target='_blank'>Créer une valeur</a>";

                } else {
                //    $str .= "+++";
                }
                $str .= "</td></tr>";
            }
        }
    }
    $str .= "<tr><td><input type='hidden' name='table' value='" . $tableName . "' />";
    $str .= "<input type='hidden' name='page' value='" . $pageDetail . "' />";
    $str .= "<input type='hidden' name='idName' value='" . $idName . "' />";
    $str .= "<input type='hidden' name='data-".$idName."' value='" . ($edit ? $id : $idMax1) . "' />";
    $str .= "<input type='hidden' name='id' value='" . ($edit ? $id : $idMax1) . "' />";
    if($tableName!="table_users") {
        $str .= "<input type='hidden' name='data-user_id' value='" . ($userData['id']) . "' />";
    }
    $str .= "<input type='hidden' name='" . $idName . "' value='" . ($edit ? $idRef : 0) . "' />";
    $str .= "<input type='hidden' name='action' value='" . ($id == 0 ? "saveNew" : "save") . "' />";
    $str .= "</td><td>
            <button id='cancelButtonTable' type='image' class='icon' value='Annuler les modifications'  onclick='goto(\"?\") '><img height='40px' width='40px' src='../images/cancel.png' alt='Annuler les modifications'/>
            <input id='submitButtonTable' class='btn-submit icon' type='image' value='Valider les modifications' height='40px' width='40px'  src='../images/validate.png' alt='Valider les modifications'/>
            </td></tr></table>";
    $str .= "</form>";

    return $str;
}

function getTableDetails($tableName): array
{
    global $db;
    $sql = "SELECT * FROM sqlite_master WHERE type ='table' AND tbl_name=:tableName;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('tableName',$tableName);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) == 0) {
        echo "Error table doesntExist()";
        return array();
    }

    $sql = $result[0]["sql"];
    $create = new PHPSQLParser($sql);

    /*echo "<p>CREATE TABLE ARRAY : <textarea>";
    print_r($create);
    echo "</p></textarea>";*/

    $fieldDetails = array();
    $fieldDetails[0] = array();
    $idField = false;
    foreach ($create->parsed["TABLE"]["create-def"]["sub_tree"] as $fieldDef) {
        if ($fieldDef["expr_type"] == 'column-def') { // KEY
            $idField = $fieldDef["sub_tree"][0]["base_expr"];// COLUMN NAME
            $typeField = $fieldDef["sub_tree"][1]["base_expr"];// COLUMN NAME
            if (isset($fieldDef["sub_tree"][1]["sub_tree"]["base_expr"]) &&
                // FOreign key???
                $fieldDef["sub_tree"][1]["sub_tree"]["expr_type"] == "foreign-ref") {//FOREIGN KEY???
                $columnsDefs = preg_split('[,]', $fieldDef["base_expr"]);
                foreach ($columnsDefs as $num => $columnDefFK) {
                    $arrayFK = preg_split('[\s]', $columnDefFK, -1, PREG_SPLIT_NO_EMPTY);

                    $idField = trim($arrayFK[0]);
                    if (!str_contains($idField, "constraint") &&
                        !str_contains($idField, "references") &&
                        !str_contains($idField, ")")) {
                        $typeField = trim($arrayFK[1]);
                        //print_r($num);
                        //print_r($arrayFK);
                        //echo "<hr/>FOREIGN KEY: " . $idField . "=" . $typeField . "<hr/>";
                        $fieldDetails[0][$idField] = $typeField;//KEY?
                        $fieldDetails["fk"]['tablename'] = true;
                    } else {
                        //echo "<b>".$fieldDef["base_expr"]."</b>";
                    }
                }
            } else {
                $fieldDetails[0][$idField] = $typeField;//KEY?
            }
            switch ($typeField) {
                case "varchar":
                    break;
                case"integer":
                case "int":
                    break;
                case  "double":
                    break;
                case "datetime":
                case "date":
                    break;

            }
            $idField = false;
        }


    }

    //print_r($fieldDetails);

    $result = $fieldDetails;
    $idRef = 0;

    //print_r($result);

    return $result;
}

function passForm($input_get, $idForm): string
{
    $str = "";
    foreach ($input_get as $name => $value) {
        //$str .= "name=" . $name . ",value=" . $value . "<br/>";
        if (strstr($name, "data-") !== NULL) {
            $input_get[substr($name, 5)] = $value;
        }
    }

    $idNameId = null;
    $result = $input_get;
    if (isset($_GET["idName"]) && isset($_GET[urldecode($_GET["idName"])])) {
        $idNameId = urldecode($_GET[urldecode($_GET["idName"])]);
        $result = getTableDetails(urldecode($_GET["table"]))[0];
    } else if (isset($_GET["id"]) && urldecode($_GET["id"]) !== null) {
        $idNameId = urldecode($_GET["id"]);
    }
    switch ($idForm) {

        case "edit":
            $str .= "<h1>Edite une ligne" . ($_GET["table"]) . "</h1>";
            $str .= printFormEdit(urldecode($_GET["table"]), urldecode($_GET["idName"]), $idNameId, NULL, false);
            break;
        case "add":
            $str .= "<h1>Ajoute une ligne dans la table " . ($_GET["table"]) . "</h1>";
            $str .= printFormEdit(urldecode($_GET["table"]), urldecode($_GET["idName"]), 0, false, NULL, false);
            break;
        case "delete":
            $str .= "<h1>Supprimer une ligne" . ($_GET["table"]) . "</h1>";
            $str .= printFormDelete(urldecode($_GET["table"]), urldecode($_GET['idName']), $idNameId);
            break;
    }
    return $str;
}

function printFormDelete(mixed $table, mixed $idName, mixed $id): string
{
    $str = "<form method='get'>";
    $str .= "<input type='hidden' name='table' value='" . $table . "' />";
    $str .= "<input type='hidden' name='idName' value='" . $idName . "' />";
    $str .= "<input type='hidden' name='id' value='" . $id . "' />";
    $str .= "<input type='hidden' name='action' value='delete' />";
    $str .= "<input type='submit' value='Delete' /></table>";
    $str .= "</form>";
    return $str;
}

function selectOptions($id_form, array $sqlAssocResult, string $idName, $idValue,
                       $arrayFields = array(), string $onChange = ""): void
{
    $i = 0;
    ?>
    <!-- onchange="function updateDataView(s) {
                let elementById = document.getElementById('<?php echo "edition_activite"; ?>');
                elementById.addEventListener('submit', (event) => {
                alert(" Submit");
        // handle the form data
        });

        }-->
    <select name="<?php echo $id_form; ?>" id="<?php echo $id_form; ?>" class="btn-choose ligne2"
            onchange="<?php echo $onChange; ?>">
        <option draggable="true" value="-1">-------</option>
        <?php while ($i < count($sqlAssocResult)) { ?>
            <option draggable="true" value="<?php echo $sqlAssocResult[$i][$idName]; ?>"
                <?php
                if (($sqlAssocResult[$i][$idName] !== null &&
                    $sqlAssocResult[$i][$idName] !== 0 &&
                    $sqlAssocResult[$i][$idName] == $idValue))
                    echo " selected='selected'  "; ?>>
                <?php
                foreach ($arrayFields as $fieldName) {
                    echo " " . $sqlAssocResult[$i][$fieldName];
                }
                ?>
            </option>
            <?php

            $i++;
        }
        ?></select>
    <a style="display: none;" href="#" ondragstart="return dragStart(event, '<?php echo $id_form; ?>')"
       onclick="dragSelectedSelectOptions('<?php echo $id_form; ?>');">Drag</a>
    <?php
}

function getDatedLink($url): string
{
    global $datetime;
    return addToLink($url, "datetime", $datetime);
}

function addToLink($url, $key, $value): string
{
    return $url . "&" . urlencode($key) . "=" . urlencode($value);
}

function addToGetUrl(string $url, $assocArrayGetKeyVPair): string
{
    if ($assocArrayGetKeyVPair == null || !is_array($assocArrayGetKeyVPair))
        return $url;
    if (!str_contains($url, "?"))
        $url = $url . "?";
    foreach ($assocArrayGetKeyVPair as $key => $value) {
        if(!is_array($value)) {
            $url .= "&" . urlencode($key) . "=" . urlencode($value);
        }else {
            $url .= addToGetUrl($url, $value);

        }
    }
    return $url;
}

if (isset($_GET["datetime"])) {
    $date = urldecode($_GET["datetime"]);
    $dateI = DateTime::createFromFormat('Y-m-d', $date);
    if ($dateI != false) {
        $date = $dateI->format("Y-m-d");
    } else {

    }
} else if (isset($_GET["j"])) {
    $dateD = date_create_from_format("Y-m-d", $_GET["a"] . "-" . $_GET["m"] . "-" . $_GET["j"]);
    $date = $dateD->format("Y-m-d");

    // $date = date_create_from_format('Y-m-d', $_GET["a"]."-".$_GET["m"]."-".$_GET["j"]);
//    $datetime  = date_format($date, 'Y-m-d');
} else {
    $dateI = date("Y-m-d", time());
    $dateD = date_create_from_format("Y-m-d", $dateI);
    $date = $dateD->format("Y-m-d");


}
$datetime = $date;
$dateCompleteInverseeNav = $date;// datetime($date);

function datetime($datetime): array
{
    global $days;
    $days_english = array("Mon", "Tues", "Wed", "Thru", "Frid", "Sat", "Sun");
    $str1 = explode(",", $datetime);
    $day_of_week_french = str_replace($days_english, $days, $str1[0]);
    $str2 = explode(" ", substr($str1[1], 1));
    $day_of_month = $str2[0];
    $month = $str2[1];
    $year = $str2[2];
    $str3 = explode(":", $str2[3]);
    $hour = $str3[0];
    $minute = $str3[1];
    $seconds = $str3[2];

    //echo "DEBUG $year/$month/$day_of_month à $hour:$minute:$seconds";
    return array($year, $month, $day_of_month, $hour, $minute, $seconds, $day_of_week_french);
}


function date_locale_fr($date_str) : string {
    $arr = explode("-", $date_str);
    if(count($arr)==3) {
        return $arr[2] . ("/" . $arr[1] . ("/" . $arr[0] . ""));
    }
    return $date_str;
}
/**
 * @param $jour__demie_heure_semaine_duree
 * @param $datetime
 * @return string for monday 00.00
 */

function real_date_for_db($jour__demie_heure_semaine_duree, $datetime)
{
    $dt = datetime($datetime);
    return $dt[0] . "-" . $dt[1] . "-" . $dt[0];
}

/**
 * @param $date
 * @param $jour__demie_heure_semaine_duree example: 0:08.30:2 (monday, at 8.30am, for 2 half hours
 * @param $datetime for monday 00.00 (without duration)
 * @return void
 */
function real_date_for_agenda($date, &$jour__demie_heure_semaine_duree, &$datetime)
{

}

function jourSemaine($date1): int
{
    # nombre de jours cumules des mois precedents modulo 7
    # sur la base d'une annee non bissextile
    $code_mois = array(0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5);
    # Extraction du jour, mois et année de la date 'jj/mm/aaaa'
    $list = explode("-", $date1);
    $j1 = $list[0];
    $m1 = $list[1];
    $a1 = $list[2];
    $j1 = (int)($j1);
    $m1 = (int)($m1);
    $a1 = (int)($a1);
    if ($j1 == 0 && $m1 == 0 && $a1 == 0)
        echo "Error joursSemaine";
    //echo "$j1 - $m1 - $a1";
    # année de référence : 1900
    $a1 = $a1 - 1900;
    # eps=1 si l'année est bissextile (=0 sinon)
    $eps = ($a1 % 400 == 0 or ($a1 % 4 == 0 and $a1 % 100 != 0)) ? 1 : 0;
    # Calcul du numéro du jour
    #r=2700 + (j%7)+(code_mois[m-1]%7)+((a+a//4)%7)-int(m<=2)*eps
    $r = ($j1 + ($code_mois[$m1 - 1])) % 7 + ($a1 + $a1 / 4) % 7 - ((int)($m1 <= 2)) * $eps;
    //echo  $r;
    return $r % 7;
}

/***
 * @param $date1
 * @param $date2
 * @return string Diff
 */
function dayDates($date1, $date2): string
{
    return date_diff(date_create($date1), date_create($date2))->format("%D");
}

function semaineAnnee($date): int
{
    list($j, $m, $a) = explode("-", $date);
    $jour1 = "01-01-$a";
    $jour2 = $date;
    return (int)((int)dayDates($jour1, $jour2) / 7);
}

function week2day($year, $week)
{

    $fdoty = date("w", mktime(0, 0, 0, 1, 1, $year));
    $days_to_second_week = 8 - $fdoty;

    $days_to_end_week = (($week - 1) * 7) + $days_to_second_week;
    $days_to_start_week = $days_to_end_week - 6;


    $daysofweek[0] = date("d-m-Y", mktime(0, 0, 0, 1, $days_to_start_week,
        $year));
    $daysofweek[1] = date("d-m-Y", mktime(0, 0, 0, 1, $days_to_end_week,
        $year));
    return $daysofweek;
}


/*
function dayCalArray($stringCal) {
    $array0 = explode(",", $stringCal);
    for($i=0; $i<7; $i++) {
        $jours[$i] = $stringCal%7;
        $stringCal = $stringCal/7;
    }
return $jours;
}
function dayCalString($array0) {
    $max = 7;
    $array1 = array();
    foreach ($array0 as $i =>$item) {
        $array1[$i] = ($item*7);
        $max *= 7;

    }
    return implode(",", $array1);
}
*/

function checkMultiple(string $string, array $resultHospitalises, array $resultPatientsTache, string $string1, array $array, string $string2, $ckecheds=null): void

{
    $onchange = $string2;
    $idx = 0;
    global $id_tache;
    global $id_hospitalise;
    foreach ($resultHospitalises as $i => $rowItem) {
        $valId = "patientCheck".rand(0, 1000);
        echo "<input id='".$valId."' onclick='".$onchange."(this)' draggable='true'  class='input' type='checkbox' name='" . $string.$idx."' value='" . ($rowItem[$string1]) . "'" . $string2 . " ";
        $selected = false;
        if (isset($resultPatientsTache) && $id_tache > 0) {
            foreach ($resultPatientsTache as $j => $rowItemPatient) {
                if($selected) {
                } else if ($rowItem[$string1] == $rowItemPatient["id_patient"]) {
                    echo(" checked='checked' ");
                    $selected = true;
                } else if (is_scalar($id_hospitalise) && array_search($rowItem[$string1], $id_hospitalise)>=0) {
                    echo(" checked='checked' ");
                    $selected = true;
                } else if (is_array($id_hospitalise) ) {
                    foreach ($id_hospitalise as $key => $value) {
                        if(array_search($rowItem[$string1], $value)>=0) {
                            echo(" checked='checked' ");
                            $selected = true;
                        }
                    }
                }
            }
        }
        echo "/><span class='checkbox_text' onclick='toggleCheckBox(\"".$valId."\"'>&nbsp;" . ($rowItem["nom"] . " " . $rowItem["prenom"]) . "&nbsp;</span>";
        $idx ++;
    }
}

function checkMultiple1(string $string, array $resultHospitalises, array $resultPatientsTache, string $string1, array $array, string $string2="chkbox(this)", $ckecheds=null): void

{
    $onchecked = $string2;
    $idx = 0;
    global $id_tache;
    foreach ($resultHospitalises as $i => $rowItem) {
        $echoed = false;
        $valId = "patientCheck".rand(0, 1000);
        echo "<input id='".$valId."' onclick='".$onchecked."(this)' draggable='true'  class='input' type='checkbox' name='" . $string.$idx."' value='" . ($rowItem[$string1]) . "'" . $string2 . " ";
        if ($id_tache > 0) {
            foreach ($resultPatientsTache as $j => $rowItemPatient) {
                if ($rowItem[$string1] == $rowItemPatient["id_patient"]) {
                    if(!$echoed) {
                        echo(" checked='checked' ");
                        $echoed = true;
                    }
                }
            }
        }
        if($id_tache>0) {
        foreach ($resultHospitalises as $chambre) {
            if($rowItem[$string1]==$chambre) {
                if(!$echoed) {
                        echo(" checked='checked' ");
                        $echoed = true;
                    }
                }

            }
        }
        echo "/><span class='checkbox_text' onclick='toggleCheckBox(\"".$valId."\"'>&nbsp;" . ($rowItem["nom"] . " " . $rowItem["prenom"]) . "&nbsp;</span>";
        $idx ++;
    }
}

/*function checkMultiple(string $string, array $resultHospitalises, $resultPatientsTache, string $string1, array $array, string $string2): void
{
    global $id_tache;
    foreach ($resultHospitalises as $i => $rowItem) {
        echo "<input class='input' type='checkbox' name='" . $string . "[]' value='" . ($rowItem[$string1]) . "'" . $string2 . " ";
        if (isset($resultPatientsTache) && $id_tache > 0) {
            foreach ($resultPatientsTache as $j => $rowItemPatient) {
                if ($rowItem[$string1] == $rowItemPatient["id_patient"]) {
                    echo("checked");
                }
            }
        }
        echo " />&nbsp;" . ($rowItem["nom"] . " " . $rowItem["prenom"]) . "&nbsp;";
    }
}

*/

function halfHourText($time) : string
{
    $timeI = (int)($time / 2);
    if ($time <= 2) {
        $heure = " heure";
    } else {
        $heure = " heures";
    }
    if ($timeI * 2 == $time) {
        $hh = ".00";
    } else {
        $hh = ".30";
    }
    return $timeI . $hh . $heure;
}
?>

