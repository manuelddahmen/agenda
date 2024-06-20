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
require __DIR__ . '/../vendor/autoload.php';

global $userData;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
global $username;
global $db;
global $page;
global $pages;

if (!isset($_GET["page"])) {
    $page = "home";
} else {
    $page = $_GET["page"];
}

$username = $username ?? (isset($_SESSION['username']) ? $_SESSION["username"] : "");
require_once "js_runphp_errors.php";
require_once "navigation.php";

$title = "Agenda (planning de la semaine) - <strong>" . (urldecode($pages["?page=$page"] ?? "Home")) . "</strong>";


if (isset($username) && isset($db)) {
    require_once "db.php";
    require_once "vue_agenda_date_mois.php";
    require_once "printTableWithGetters.php";
}
require_once "logon.php";
$exit_after = false;
if (function_exists("logon")) {
    if (!checkLoginForm()) {
        if (function_exists("login")) {
            login();
        } else {
            //$exit_after = true;
        }
    }
}
require_once "AgendaUser.php";
if ($username != NULL) {
    $userDetails = new AgendaUser($username);

    $userDetails = $userDetails->getData();

    if ($userDetails != NULL) {
        $themeName = $userDetails["theme_name"];
    }
}
global $page, $pages, $themeName;
?><!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../css/light/agenda.css" type="text/css">
    <link rel="stylesheet" href="../css/light/search_menu.css" type="text/css">
    <link rel="stylesheet" href="../css/light/print.css" type="text/css" media="print">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script src="https://www.gstatic.com/firebasejs/8.0/firebase-app.js"></script>
    <script src="../js/google-firebase.js" type="module">

    </script>


    <!-- Latest compiled and minified CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <!-- Optional theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script type="text/javascript" src="agenda.js"></script>
    <script type="text/javascript" src="../js/notifications.js"></script>

    <script type="text/javascript">
        const tableToExcel = (function () {
            const uri = 'data:application/vnd.ms-excel;base64,'
                ,
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><²><table>{table}</table></body></html>'
                , base64 = function (s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                }
                , format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                };
            return function () {
                const table = document.getElementById("agenda");
                const name = "Agenda Excel";
                const ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
                window.location.href = uri + base64(format(template, ctx))
            }

        })();

        //    openWindow(url);
    </script>
    <?php
    if (isset($_GET["accept_cookies"]) && $_GET["accept_cookies"] == "true") {
        // ++ Vérifier qu'il n'y ait pas de referrer
        session_set_cookie_params(array("lifetime" => time() + 3600 * 24));
        setcookie("accept_cookies", "true", time() + 60 * 60 * 24 * 30, "/",
            "empty3.app", true, false);
        session_commit();
        header("Location: index.php\n");
        exit;
    }
    if (isset($_COOKIE['accept_cookies'])) {
        $show_cookie = false;
    } else {
        $show_cookie = true;
    } ?>
    <script defer src="cookiechoices.js"></script>
    <script>

        function auth_popup(provider) {
            // replace 'path/to/hybridauth' with the real path to this script
            var authWindow = window.open('https://empty3.app/agenda/src/?page=login&?provider=' + provider, 'authWindow', 'width=600,height=400,scrollbars=yes');
            window.closeAuthWindow = function () {
                authWindow.close();
            }

            return false;
        }
    </script>
</head>
<body>
<div class="" onload="page_onLoad();">

    <div id="title_page">
        <a href="?">
            <h1>
                <img height="40px" width="40px"
                     src="../images/favicon.png"/><?php echo $title . " - " . date("d-m-y"); ?>

            </h1>
        </a>
    </div>
    <?php /*
<div id="session_user">
    <ul id="isConnected"><?php if ($username==NULL ||strlen($username)==0) {
        echo "<li class='user_connection'><span class='none'>Pas d'utilisateur en session</span></li>";
            echo "<li class='user_connection'><span class='none'><a href='index.php?page=login'>Se connecter</a></span>";
            echo "<li class='user_connection'><a href='index.php?page=create_user'>Créer un compte</li>";
    }else {
            echo "<span class='valid'>Connecté</span><ul>";
            global $logins;
            $i = 0;
            foreach ($logins as $login) {
            $i++;
            ?>
    </ul>
    <p id="username"><?php echo $login["username"]; ?></p>
    <?php if($login["password"]!=NULL) { ?>
        <li>Login #<?php echo $i; ?> : Site login</li>
        <?php } else { ?>
        <li>Login #<?php echo $i; ?> : OAuth2 Service : <?php echo $login["service"]; ?></li>
    <?php
    }
    }
    ?></ul><?php
    }
    ?>
    <p id="errorLogin"></p>
</div>
<?php
*/


    displayNav();


    require_once "main_menu.php";

    //echo "UserId : ".$userData["id"];

    //require_once "search_menu.php";

    echo "<a class='date' style='float: right' href='" . make_link("?") . "'>Aujourd'hui:" . date("d/m/Y") . "</a><br/>";
    global $j, $m, $a;
    $timestamp = date_create("" . ($_GET['a'] ?? $a) . "-" . ($_GET['m'] ?? $m) . "-" . ($_GET['j'] ?? $j));
    //
    //if($timestamp!==false) {
    //    echo "<button style='float: right'>Date du calendrier: " . date("d/m/Y", $timestamp->getTimestamp()) . "</button><br/>";
    //    display_calendar($_GET['j'] ?? $j, $_GET['m'] ?? $m, $_GET['a'] ?? $a);
    //}
    //echo "<p><a style='float: right' href='".make_link("?page=help")."' target='_blank' ><img id='help_img' alt='Search helper' src='../images/help.png' /></a></p>";

    global $loginForm;
    echo '<a href="?page=login">' . $loginForm . '</a>';
    global $pagesNames;
    $page_id = array_search("?page=" . $page, $pagesNames);
    if ($page_id >= 0) {
    $page_next = make_link($pagesNames[((int)($page_id) + 1 + count($pagesNames)) % (count($pagesNames))]);
    $page_prev = make_link($pagesNames[((int)($page_id) - 1 + count($pagesNames)) % (count($pagesNames))]);

    if ($username == "" || $username == NULL) {
        $username = "anonyme";
    }
    ?>
    <!--<h3 class="user nickname">Utilisateur: <?php echo $username; ?></h3>-->
<?php
}
//if($exit_after) exit;


global $username;
if (!isset($username) || strlen($username) == 0) {
    $username = "anonyme";
}
?>