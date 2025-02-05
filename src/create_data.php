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

require_once "db.php";
global $username, $db, $currentPage;

if (!isset($username) || strlen($username) == 0 || $username == "anonyme" || $currentPage == "signup") {
    $username = null;
    $db = new MyDB();
    //print_r($db);
    echo printFormEdit("table_users", "id",0, false, array(), true);
}

global $strContent;
echo $strContent;

if (isset($str)) {
    echo $str;
}
if(db_make_a_copy($username)) {
    echo "<H3>Succès: Données créées</H3>";
} else {
    ?><div style="color: red;"><h2>Erreurs: données existent déjà, erreur, ou requête invalides</h2></div>
<?php
}
?>