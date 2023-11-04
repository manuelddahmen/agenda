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


global $userData;
if($userData==NULL) {
    echo "<h2>Non connecté</h2>";
    exit(0);
}
global $username;
if(!isset($username) || strlen($username)==0){
    $username = "anonyme";
}

global $strContent;
echo $strContent;

if (isset($str)) {
    echo $str;
}

?><h1>Page des paramètres et déconnection</h1>
<h2><a href="index.php?logout=Google&Logout=site">Logouts</a></h2>
<h2><a href="index.php?email=confirmationLink">Confirmer l'email</a></h2>