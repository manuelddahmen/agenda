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

require_once "MyDB.php";
function checkLoginForm(): bool
{
    global $connected;
    global $loginForm, $logins;
    $loginForm = "";

    $passwords_db = new MyDB(/*"passwords"*/);
    global $username;

    if(isset($logins) && count($logins)>0) {
        $username = $logins[0]["username"];
        return true;
    }

    if ((isset($_SESSION['username']) && isset($_SESSION["password"]))) {
        if (checkLogin($_SESSION['username'], $_SESSION['password'])) {
            $username = $_SESSION['username'];
            $connected = true;
            $loginForm = "<h3 for'loggedIn'>$username</h3>";
            return true;
        } else {
            $loginForm = "<li class='sousmenu '>Erreur login<a href='index.php?page=login'>Reconnectez-vous</a></li>";
            return false;
        }
    } else {
        $loginForm = "<li class='sousmenu '>Erreur login (pas de session)<a href='index.php?page=login'>Reconnectez-vous</a></li><script type='text/javascript'>//document.location.href='index.php';</script>";
        $loginForm .= "<li class='sousmenu '>Créer un compte<a href='index.php?page=create_user'>Créer un compte</a></li><script type='text/javascript'>//document.location.href='index.php';</script>";
        if(isset($_SESSION["username"])) unset($_SESSION["username"]);
        if(isset($_SESSION["password"])) unset($_SESSION["password"]);


    }
    return false;
}


function checkLogin(mixed $username, mixed $password): bool
{
    global $logins, $username;
    if(isset($logins) &&count($logins)>0) {
        $username = $logins[0]["username"];
        return true;
    }
    $db = new MyDB(/*$username*/);
    if (strlen($username)>2 && strlen($password)>3) {
        require_once "db.php";
        global $db;
        if($db==null) {
            echo "Erreur : pas de connection.";
            return false;
        }
        $sql = "SELECT count(*)  as yes FROM table_users where username = '" . $username . "' and password ='" . $password . "';";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $response = $stmt->fetchAll();
        $count = $response[0]['yes'];
        if ($count == 1) // nom d'utilisateur et mot de passe corrects
        {
            return true;
        } else {
            $db = null;
            echo "Erreur pas d'utilisateur $username";
            echo "<a href='index.php?page=create_user'>Créer un utilisateur</a>";
            return false;
        }
    }
    $db = null;
    echo "Erreur Paire incorrecte";
    return false;
}