<?php
/*
 * Copyright (c) 2024. Manuel Daniel Dahmen
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

chdir("..");
/*
 * Copyright (c) 2023. Manuel Daniel Dahmen
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

/*
 * Coche / décoche -> Mettre à jour la vue semaine.
Drag and Drop:
1) Drag sur activité: ajout à la tâche (icône 1)
2) Drag sur case vide : Création tâche
3) Drag de planning à poubelle : Drag tâche -> poubelle
4) Drag de planning à poubelle : Drag patient -> poubelle
   Effacer activité si activité vide avec confirmation.
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
require_once "AgendaUser.php";
if($username!=NULL) {
    $userDetails = new AgendaUser($username);

    $userDetails = $userDetails->getData();

    if($userDetails!=NULL) {
        $themeName = $userDetails["theme_name"];
    }
}

?>