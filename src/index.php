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

global $logins;
$logins = array();

require_once('../vendor/autoload.php');
require_once('config.php');
require_once "functions.php";
//require_once "login.php";

use Hybridauth\Hybridauth;

global $config;
try {
    $hybridauth = new Hybridauth($config);
    $adapters = $hybridauth->getConnectedAdapters();
} catch (\Hybridauth\Exception\InvalidArgumentException|RuntimeException|\Hybridauth\Exception\UnexpectedValueException|Exception|Throwable $e) {
    //print_r($e);
}


global $username;


require_once "framework.php";

?>

        <?php if(!($username!=null && strlen($username)>0)) {
        ?>
    <div id="signIn">
    <h1>Sign in</h1>
<div class="g-signin2" data-onsuccess="onSignIn"></div>
<a href="#" onclick="signOut();">Sign out</a>
<ul id="login_list">

    <?php foreach ($hybridauth->getProviders() as $name) { ?>
        <?php if (!isset($adapters[$name])) { ?>
            <li>
                <a href="#" onclick="javascript:auth_popup('<?php print $name ?>');"><?php
if($name=="Google") {
?>
<img src="../images/google_signin_buttons/web/2x/btn_google_signin_light_normal_web@2x.png"
     alt="Sign in with <?php print $name ?>" />
                        <?php
    } else { ?>Sign in with <?php print $name ?><?php

    }
    ?></a>
            </li>
        <?php } ?>
    <?php } ?>

    <li  id='login_site_link'><a href="?page=login_site_login" class="btn-choose btn">Connexion à l'application avec votre
            adresse e-mail</a></li>
</ul>
    </div>
<div id="login">

<?php

if ($adapters) : ?>
    <h1>You are logged in:</h1>
    <ul>
        <?php foreach ($adapters as $name => $adapter) :
            $userProfile = $adapter->getUserProfile(); ?>
            <li>
                <strong><?php echo $userProfile->displayName; ?></strong> from
                <i><?php print $name; ?></i>
                <span>(<a href="<?php print $config['callback'] . "?logout={$name}"; ?>&page=logout">Log Out</a>)</span>
                <p>Entrer dans l'application en tant que <?php echo $username = $userProfile->email;

                    $databaseFilenameBasedOnEmail = $userProfile->email;
                    //str_replace("@", "-", $userProfile->email);
                    $databaseFilenameBasedOnEmail = "../data_agenda/database_agenda_" . $databaseFilenameBasedOnEmail;
                    if (file_exists($databaseFilenameBasedOnEmail)) {
                        echo "Data exists for you";
                        global $dbFilename;
                        global $username;
                        $username = $userProfile->email;
                        $_SESSION["username"] = $username;
                        $dbFilename = $databaseFilenameBasedOnEmail;
                        $logins[] = array("username" => $username, "password" => "''", "authority"=>$name);
                    } else {
                        echo "No data associated with this account";
                        echo "<a href='?create_user'>Créer la base de données.</a>";
                    }
                    ?></p>
            </li>
        <?php endforeach; ?>
<?php endif; ?>
    </ul>
</div>
<?php

}
global $currentPage;
$currentPage = "";
if (isset($_GET["page"])) {
    $currentPage = escapeSqlChars($_GET["page"]);

}

global $editAddDeleteStr;
?><div id="float-windows"><?php

echo $editAddDeleteStr;
?></div><?php

if(isset($username) && $currentPage=="")
    $currentPage = "tables";

switch ($currentPage) {
    case "home":
        require_once "home.php";
        break;
    case "":
    case "tables":
        require_once "agenda.php";
        break;
    case "adacti":
    case "acti":
        require_once "vue_gestion_activites.php";
        break;
    case "pati":
        require_once "vue_gestion_patients.php";
        break;
    case "memb":
        require_once "vue_gestion_membres_du_personnel.php";
        break;
    case "create_data":
    case "create_user":
        require_once "create_user.php";
        break;
    case "advent":
        require_once "framework.php";
        require_once "vue_edition_tache.php";
        break;
    case "view_event":
        require_once "vue_edition_tache.php";
        break;
    case "agenda":
        require_once "vue_manipe.php";
        break;
    case "login":
    case "login_site_login":
        if(isset($_POST["lost_password"])) {
            require_once "lost_password.php";
        } else {
            require_once "login2.php";
        }
        break;
    case "send_password_reset_link":
        require_once "send_password_reset_link.php";
        break;
    case "recover_password":
        require_once "recover_password.php";
        break;
    case "privacy_policy":
        require_once "privacy_policy.php";
        break;
    case "help":
        require_once "help.php";
        break;
    case "signout" :
    case "logout":
        require_once "logout.php";
        break;
    case "autonomies":
        require_once "autonomies.php";
        break;
    case "password":
        require_once "password.php";
        break;
    case "parametres":
        require_once "parametres.php";
        break;
    case "profile":
        require_once "profile.php";
        break;
    default:
        require_once "404.php";
        break;

}
?>
    </div>
<?php
session_commit();
?>