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

$tableName = $_GET['table'];
$id = $_GET['id'];
$action = $_GET["action"];
if($action=="editTableLine") {
    return getEditFormInPlaceTable($tableName, $id);
} else if($action=="saveTableLine") {
    return saveItem($tableName, $id);
}
function getEditFormInPlaceTable($tableName, $id) {
    $tableStruct = getTableDetails($tableName);
    //print_r($tableStruct);
    echo printFormEdit($tableName, "chambre", $id, true,  array(), true);
}
function saveItem($tableName, $id) {
    // Save item
    $tableStruct = getTableDetails($tableName);
    print_r($tableStruct);
    // Return saved line or error

}